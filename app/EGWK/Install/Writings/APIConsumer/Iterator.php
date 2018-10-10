<?php

namespace App\EGWK\Install\Writings\APIConsumer;

use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;

/**
 * Iterator
 *
 * @author Peter
 */
class Iterator
{

    /**
     * Request retry limit
     */
    const RETRY_LIMIT = 10;

    /**
     * Sleep before retrying request
     */
    const SLEEP_BEFORE_RETRY = 5;

    /**
     * Default language(Config::install.language)
     */
    const LANGUAGE = 'en';

    /**
     * Folders skip / enable key
     */
    const LIST_TOP_FOLDERS = 'top';

    /**
     * Folders skip / enable key
     */
    const LIST_FOLDERS = 'folders';

    /**
     * Book titles skip / enable key
     */
    const LIST_TITLES = 'titles';

    /**
     * Book codes skip / enable key
     */
    const LIST_CODES = 'codes';

    /**
     * Suthors skip / enable key
     */
    const LIST_AUTHORS = 'authors';

    /**
     * @var Request Request object
     */
    protected $request = null;

    /**
     * Class constructor
     *
     * @access public
     * @param Request $request Request object
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Is item enabled solely?
     *
     * @access protected
     * @return boolean
     */
    protected function isEnabled($name, $list)
    {
        $listArray = config('install.enable.' . $list, []);
        return empty($listArray) || in_array($name, $listArray);
    }

    /**
     * Is the item to be skipped?
     *
     * @access protected
     * @return boolean
     */
    protected function isSkipped($name, $list)
    {
        return in_array($name, config('install.skip.' . $list, []));
    }

    /**
     * Requests and iterates through folders
     *
     * @access public
     * @return \stdClass JSON node
     */
    public function writings()
    {
        $language = config('install.language', self::LANGUAGE);
        foreach ($this->request->get("/content/languages/$language/folders/") as $topFolder) {
            if ($this->isEnabled($topFolder->name, self::LIST_TOP_FOLDERS)) {
                yield from $this->writingsChildren($topFolder->children);
            }
        }
    }

    /**
     * Requests and iterates through sub-folders recursively
     *
     * @access protected
     * @param array $children List of sub-folders
     * @return StdClass JSON node
     */
    protected function writingsChildren($children)
    {
        foreach ($children as $folder) {
            if (
                !$this->isSkipped($folder->name, self::LIST_FOLDERS)
                &&
                $this->isEnabled($folder->name, self::LIST_FOLDERS)
            ) {
                if (!empty($folder->children)) {
                    yield from $this->writingsChildren($folder->children);
                } else {
                    yield $folder;
                }
            }
        }
    }

    /**
     * Requests and iterates through a chapter's paragraphs
     *
     * @access public
     * @param string $bookId Book ID
     * @param string $chapterId Chapter ID
     * @return StdClass JSON paragraph
     */
    public function chapter($bookId, $chapterId)
    {
        foreach ($this->iterate("/content/books/$bookId/chapter/$chapterId/") as $paragraph) {
            yield $paragraph;
        }
    }

    /**
     * Requests and iterates through a book's TOC
     *
     * @access public
     * @param string $bookId Book ID
     * @return StdClass JSON TOC entry
     */
    public function toc($bookId)
    {
        foreach ($this->iterate("/content/books/$bookId/toc/") as $entry) {
            yield $entry;
        }
    }

    /**
     * Requests and iterates through several paragraphs
     *
     * @access public
     * @param string $bookId Book ID
     * @param string $idElement ID Element
     * @return StdClass JSON paragraph
     */
    public function paragraphs($bookId, $idElement)
    {
        foreach ($this->iterate("/content/books/$bookId/content/$idElement/") as $paragraph) {
            yield $paragraph;
        }
    }

    /**
     * Requests and iterates through list of books in a folder
     *
     * @access public
     * @param string $folder Book ID
     * @return StdClass JSON paragraph
     */
    public function books($folder = null)
    {
        $byFolder = null === $folder ? '' : "by_folder/$folder/";
        foreach ($this->iterate('/content/books/' . $byFolder) as $book) {
            if (
                !$this->isSkipped($book->code, self::LIST_CODES)
                &&
                $this->isEnabled($book->code, self::LIST_CODES)
                &&
                !$this->isSkipped($book->title, self::LIST_TITLES)
                &&
                $this->isEnabled($book->title, self::LIST_TITLES)
                &&
                !$this->isSkipped($book->author, self::LIST_AUTHORS)
                &&
                $this->isEnabled($book->author, self::LIST_AUTHORS)
            ) {
                yield $book;
            }
        }
    }

    /**
     * Iterates through result pages
     *
     * @access protected
     * @param StdClass $parentItem Parent item
     * @param string $nextField Next field
     * @return StdClass JSON page
     */
    protected function resultPages($parentItem, string $nextField)
    {
        $hasNext = false;
        do {
            foreach ($parentItem->results as $item) {
                yield $item;
            }
            $hasNext = $parentItem->{$nextField} !== null;
            if ($hasNext) {
                $parentItem = $this->request->getAPIConsumer()->request('GET', $parentItem->{$nextField});
            }
        } while ($hasNext);
    }

    /**
     * Iterates through result items
     *
     * @access protected
     * @param array $items Next field
     * @return StdClass JSON item
     */
    protected function resultItems(array $items)
    {
        foreach ($items as $item) {
            yield $item;
        }
    }

    /**
     * Iterator base
     *
     * @access protected
     * @param string $command Command
     * @param array $parameters Parameters
     * @param string $nextField Next field
     * @return \stdClass JSON item
     */
    protected function iterate(string $command, $parameters = [], $nextField = "next")
    {

        $success = false;
        for ($counter = 0; $counter < self::RETRY_LIMIT && !$success; $counter++) {
            try {
                $items = $this->request->get($command, $parameters);
                $success = true;
            } catch (\Exception $e) {
                sleep(self::SLEEP_BEFORE_RETRY);
                Log::warning($e->getMessage());
            }
        }
        if (null !== $items) {
            if (!isset($items->results)) {
                yield from $this->resultItems($items);
            } else {
                yield from $this->resultPages($items, $nextField);
            }
        }
        return (object)[];
    }

}
