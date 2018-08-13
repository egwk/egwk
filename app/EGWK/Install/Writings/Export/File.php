<?php

namespace App\EGWK\Install\Writings\Export;

use App\EGWK\Install\Writings\Filter;

/**
 * File
 *
 * @author Peter
 */
abstract class File extends Export
{

    /**
     *
     * @var string Output file name
     */
    protected $outputFile = "";

    /**
     * Class constructor
     *
     * @access public
     * @param Filter $filter Filter object
     * @param string Output file name
     * @return void
     */
    public function __construct(Filter $filter, $outputFile = "./data")
    {
        parent::__construct($filter);
        $this->initOutputFile($outputFile);
    }

    /**
     * Initializes output file
     *
     * @access protected
     * @param string Output file name
     * @return void
     */
    protected abstract function initOutputFile(string $outputFile = "./data");

    /**
     * Adds file name modifier before extension
     *
     * @access protected
     * @param string $outputFile Output file name
     * @param string $modifier File name modifier
     * @return string
     */
    protected function addFileNameModifier(string $outputFile, string $modifier)
    {
        $pathParts = pathinfo($outputFile);

        return $pathParts['dirname']
                . DIRECTORY_SEPARATOR
                . $pathParts['filename']
                . ".$modifier."
                . $pathParts['extension'];
    }

    /**
     * Resets output file
     *
     * @access protected
     * @param string $modifier File name modifier
     * @return void
     */
    protected function resetOutputFile($modifier = "")
    {
        $this->writeOutputFileBase('', $modifier);
    }

    /**
     * Writes data into output file
     *
     * @access protected
     * @param string $data Data
     * @param string $modifier File name modifier
     * @return void
     */
    protected function writeOutputFile($data, $modifier = "")
    {
        $this->writeOutputFileBase($data, $modifier, FILE_APPEND);
    }

    /**
     * Base function for file writing
     *
     * @access protected
     * @param string $data Data
     * @param string $modifier File name modifier
     * @param int $flags Flags [see: file_put_contents()]
     * @return void
     */
    protected function writeOutputFileBase($data, $modifier = "", int $flags = 0)
    {
        file_put_contents($this->addFileNameModifier($this->outputFile, $modifier), $data, $flags);
    }

}
