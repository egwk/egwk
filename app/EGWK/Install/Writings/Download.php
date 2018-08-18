<?php

namespace App\EGWK\Install\Writings;

/**
 * Process writings
 * Downloads paragraphs by iterating through
 *      folders
 *          -> books
 *              -> TOCs
 *                  -> chapters
 *                      -> paragraphs
 *
 * @author Peter
 */
class Download
{

    use \App\EGWK\Tools\OperationCounter;
    use \App\EGWK\Tools\ProcessLog;

    /**
     *
     * @var Store $store
     */
    protected $store = null;

    /**
     *
     * @var APIConsumer\Iterator $iterator
     */
    protected $iterator = null;

    /**
     * Skip books until this book code is reached.
     *
     * @var string
     */
    protected $skipTo = null;

    /**
     * @var string Skip data to a specific paragraph ID
     */
    protected $skipToParaID = null;

    /**
     * Class constructor
     *
     * @access public
     * @param \App\EGWK\Install\Writings\APIConsumer\Iterator $iterator
     * @param \App\EGWK\Install\Writings\Store $store
     * @return void
     */
    public function __construct(APIConsumer\Iterator $iterator, Store $store)
    {
        $this->store = $store;
        $this->iterator = $iterator;
        $this->initCounter(0); // set positive value for testing 
    }

    /**
     * @param $code Book code or paragraph ID
     */
    public function setSkipTo($code)
    {
        $this->skipTo = trim($code);
        if (preg_match('/^[0-9]+\.[0-9]+$/', $this->skipTo)) {
            // Note: using paragraph-level skip is ignorant of parents!
            $this->skipToParaID = $this->skipTo;
            $this->skipTo = preg_replace('/^([0-9]+)\.[0-9]+$/', '$1', $this->skipTo);
        }
    }

    /**
     * Process chapter, store paragraphs
     *
     * @access protected
     * @param \stdClass $tocEntry
     * @return void
     */
    protected function chapter(\stdClass $tocEntry)
    {
        $this->logProc([$tocEntry->para_id, $tocEntry->title], 3);
        list($bookId, $idElement) = explode('.', $tocEntry->para_id, 2);
        foreach ($this->iterator->chapter($bookId, $idElement) as $paragraph) {
            if (null !== $this->skipToParaID && $paragraph->para_id !== $this->skipToParaID) {
                continue;
            } elseif (null !== $this->skipToParaID) {
                $this->skipToParaID = null;
            }
            $this->logTick();
            $this->store->store($paragraph);
            $this->stepCounter();
            if ($this->getOperationTermSignal()) {
                break;
            }
        }
        $this->logBr();
    }

    /**
     * Process Table of Contents, store chapters
     *
     * @access protected
     * @param \stdClass $book
     * @return void
     */
    protected function toc(\stdClass $book)
    {
        $this->logProc([$book->code, $book->title], 1);
        foreach ($this->iterator->toc($book->book_id) as $tocEntry) {
            $this->chapter($tocEntry);
            if ($this->getOperationTermSignal()) {
                break;
            }
        }
    }

    /**
     * Process books, store Table of Contents
     *
     * @access protected
     * @param \stdClass $folder
     * @return void
     */
    protected function books(\stdClass $folder)
    {
        $this->logProc([$folder->folder_id, $folder->name], 0, "-");
        foreach ($this->iterator->books($folder->folder_id) as $book) {

            if (!empty($this->skipTo)) {
                if (null !== $this->skipTo && $book->book_id != $this->skipTo && $book->code !== $this->skipTo) {
                    continue;
                } elseif (null !== $this->skipTo) {
                    $this->skipTo = null;
                }
            }

            $this->toc($book);
            if ($this->getOperationTermSignal()) {
                break;
            }
        }
    }

    /**
     * Process writings folders, store books.
     *
     * @access public
     * @return void
     */
    public function writings()
    {
        $this->store->begin();
        foreach ($this->iterator->writings() as $folder) {
            $this->books($folder);
            if ($this->getOperationTermSignal()) {
                break;
            }
        }
        $this->store->end();
    }

}
