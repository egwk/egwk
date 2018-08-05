<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 05/08/2018
 * Time: 13:27
 */

namespace App\EGWK;


abstract class Parser
{

    protected $filename = "";
    protected $data = "";
    protected $parsed = [];

    public function __construct($filemane)
    {
        $this->filename = $filemane;
        $this->read();
    }

    /**
     * @param string $filename
     * @return Parser
     */
    public function setFilename($filename): Parser
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return Parser
     */
    public function read()
    {
        $this->data = file_get_contents($this->filename);
        return $this;
    }

    /**
     * @return array
     */
    public function getParsed(): array
    {
        return $this->parsed;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    public abstract function parse();
    public abstract function filter();
    public abstract function tokenize();
}
