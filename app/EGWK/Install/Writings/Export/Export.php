<?php

namespace App\EGWK\Install\Writings\Export;

use App\EGWK\Install\Writings\Filter;

/**
 * Export
 *
 * @author Peter
 */
abstract class Export
{

    /**
     *
     * @var Filter Filter object
     */
    protected $filter = null;

    /**
     *
     * @var Filter\Sentence Sentence filter object
     */
    protected $sentenceFilter = null;

    /**
     *
     * @var Filter\Wrapper\Chain Chain filter object
     */
    protected $chainFilter = null;

    /**
     *
     * @var Filter\Wrapper\Chain\Sentence Sentence Chain filter object
     */
    protected $sentenceChainFilter = null;

    /**
     * Exports a single paragraph
     *
     * @access public
     * @param mixed $paragraph Paragraph text or object
     * @return mixed
     */
    public abstract function export($paragraph);

    /**
     * Class constructor
     *
     * @access public
     * @param Filter $filter Filter object
     * @return void
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
        $this->sentenceFilter = new Filter\Sentence($this->filter);
        $this->chainFilter = new Filter\Wrapper\Chain($this->filter);
        $this->sentenceChainFilter = new Filter\Wrapper\Chain\Sentence($this->sentenceFilter);
    }

}
