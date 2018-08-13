<?php

namespace App\EGWK\Install\Translations;

/**
 * DataFile
 *
 * @author Peter
 */
abstract class DataFile
{

    /**
     * DataFile Factory
     * 
     * @param string $dataType
     * @param string $dataFilePath
     * @return \App\EGWK\Install\Translations\DataFile
     */
    public static function factory(string $dataType, string $dataFilePath): \App\EGWK\Install\Translations\DataFile
    {
        return new $dataType($dataFilePath);
    }

    /**
     * DataFile Factory
     * 
     * @param string $dataType
     * @param string $dataFilePath
     * @return \App\EGWK\Install\Translations\DataFile
     */
    public static function extensionFor(string $dataType): string
    {
        switch ($dataType)
        {
            case DataFile\Excel\Filtered::class:
                return '.xlsx';
            default:
                return '.txt';
        }
    }

    /**
     * Gets number of rows
     * 
     * @return int
     */
    public abstract function getRowNum(): int;

    /**
     * Iterates through data
     * 
     * @return array
     */
    public abstract function iterate(): \Iterator;

    /**
     * Gets current Original data
     * 
     * @return mixed
     */
    public abstract function getOriginal();

    /**
     * Gets current Translation data
     * 
     * @return mixed
     */
    public abstract function getTranslation();
}
