<?php

namespace App\EGWK\Install\Writings\Store;

use App\EGWK\Install\Writings\Filter;
use App\EGWK\Install\Writings\Store;
use Illuminate\Support\Facades\Storage;

/**
 * File
 *
 * @author Peter
 */
abstract class File extends Store
{

    /**
     *
     * @var string Output file name
     */
    protected $outputFile = "";

    /**
     * @var bool
     */
    protected $reset = true;

    /**
     * Class constructor
     *
     * @access public
     * @param Filter $filter Filter object
     * @param string Output file name
     * @return void
     */
    public function __construct(Filter $filter, $outputFile = "./data", $reset = true)
    {
        parent::__construct($filter);
        $this->reset = $reset;
        $this->outputFile = $outputFile;
    }

    /**
     * @inheritdoc
     */
    public function begin()
    {
        $this->initOutputFile($this->outputFile);
    }

    /**
     * @inheritdoc
     */
    public function end()
    {
        // Nothing to do
    }

    /**
     * @inheritdoc
     */
    protected function before()
    {
        // Nothing to do
    }

    /**
     * @inheritdoc
     */
    protected function after()
    {
        // Nothing to do
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
        Storage::delete($this->outputFile);
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
        file_put_contents(Storage::path($this->addFileNameModifier($this->outputFile, $modifier)), $data, FILE_APPEND);
        // Note: Storage::append resets the file in vain
        // Storage::append($this->addFileNameModifier($this->outputFile, $modifier), $data);
    }


}
