<?php

namespace App\EGWK\Install\Writings\APIConsumer;

use App\EGWK\Install\Writings\APIConsumer\Request;
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
     * Default value for folder filter (Config::install.skip_folder)
     */
    const SKIP_FOLDER_LIST = [];

    /**
     * Request retry limit
     */
    const RETRY_LIMIT = 5;

    /**
     * Default language(Config::install.language)
     */
    const LANGUAGE = 'en';

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
     * Requests and iterates through folders
     *
     * @access public
     * @return StdClass JSON node
     */
    public function writings()
    {
        $language = config('install.language', self::LANGUAGE);
        foreach ($this->request->get("/content/languages/$language/folders/") as $topFolder) {
            if (config('install.top_folder', 'EGW Writings') == $topFolder->name) {
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
            if (!in_array($folder->name, config('install.skip_folder', self::SKIP_FOLDER_LIST))) {
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
            yield $book;
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
     * @return StdClass JSON item
     */
    protected function iterate(string $command, $parameters = [], $nextField = "next")
    {

        $success = false;
        for ($counter = 0; $counter < self::RETRY_LIMIT && !$success; $counter++) {
            try {
                $items = $this->request->get($command, $parameters);
                $success = true;
            } catch (ServerException $e) {
                sleep(2);
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
        return [];
    }

}
