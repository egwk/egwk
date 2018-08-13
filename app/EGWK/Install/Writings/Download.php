<?php

namespace App\EGWK\Install\Writings;

/**
 * Process writings
 * Exports paragraphs by iterating through
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

    use \App\EGWK\Install\Writings\Tools\OperationCounter;
    use \App\EGWK\Install\Writings\Tools\ProcessLog;

    /**
     *
     * @var Export\ExportInterface $export 
     */
    protected $export = null;

    /**
     *
     * @var APIConsumer\Iterator $iterator 
     */
    protected $iterator = null;

    /**
     * Class constructor
     *  
     * @access public
     * @param \App\EGWK\Install\Writings\APIConsumer\Iterator $iterator
     * @param \App\EGWK\Install\Writings\Export\Export $export
     * @return void
     */
    public function __construct(APIConsumer\Iterator $iterator, Export\Export $export)
    {
        $this->export = $export;
        $this->iterator = $iterator;
        $this->initCounter(0); // set positive value for testing 
    }

    /**
     * Process chapter, export paragraphs
     *  
     * @access protected
     * @param \stdClass $tocEntry
     * @return void
     */
    protected function chapter(\stdClass $tocEntry)
    {
        $this->logProc([$tocEntry->para_id, $tocEntry->title], 3);
        list($bookId, $idElement) = explode('.', $tocEntry->para_id, 2);
        foreach ($this->iterator->chapter($bookId, $idElement) as $paragraph)
        {
            $this->logTick();
            $this->export->export($paragraph);
            $this->stepCounter();
            if ($this->getOperationTermSignal())
            {
                break;
            }
        }
        $this->logBr();
    }

    /**
     * Process Table of Contents, export chapters
     *  
     * @access protected
     * @param \stdClass $book
     * @return void
     */
    protected function toc(\stdClass $book)
    {
        $this->logProc([$book->code, $book->title], 1);
        foreach ($this->iterator->toc($book->book_id) as $tocEntry)
        {
            $this->chapter($tocEntry);
            if ($this->getOperationTermSignal())
            {
                break;
            }
        }
    }

    /**
     * Process books, export Table of Contents
     *  
     * @access protected
     * @param \stdClass $folder
     * @return void
     */
    protected function books(\stdClass $folder)
    {
        $this->logProc([$folder->folder_id, $folder->name], 0, "-");
        foreach ($this->iterator->books($folder->folder_id) as $book)
        {
            $this->toc($book);
            if ($this->getOperationTermSignal())
            {
                break;
            }
        }
    }

    /**
     * Process writings folders, export books.
     *  
     * @access public
     * @return void
     */
    public function writings()
    {
        foreach ($this->iterator->writings() as $folder)
        {
            $this->books($folder);
            if ($this->getOperationTermSignal())
            {
                break;
            }
        }
    }

}
