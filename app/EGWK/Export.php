<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 07/08/2018
 * Time: 23:05
 */

namespace App\EGWK;


abstract class Export
{
    protected $data;
    protected $targetFile;

    abstract public function compile();
    abstract public function export();

    /**
     * @return mixed
     */
    public function getTargetFile()
    {
        return $this->targetFile;
    }

    /**
     * @param mixed $targetFile
     */
    public function setTargetFile($targetFile): void
    {
        $this->targetFile = $targetFile;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }


}
