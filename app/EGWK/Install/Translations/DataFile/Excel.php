<?php

namespace App\EGWK\Install\Translations\DataFile;

use App\EGWK\Install\Translations\DataFile;

/**
 * Excel
 *
 * @author Peter
 */
abstract class Excel extends DataFile
{

    /**
     * Paragraph glue 
     */
    const PARAGRAPH_GLUE = ' ';

    /**
     *
     * @var \PHPExcel 
     */
    protected $PHPExcelObject;

    /**
     *
     * @var \PHPExcel_Worksheet 
     */
    protected $workSheet;

    /**
     *
     * @var array
     */
    protected $currentRow;

    /**
     *
     * @var array
     */
    protected $lastRow;

    /**
     * Class constructor
     * 
     * @param string $excelFilePath Excel File Path
     * @return void
     */
    public function __construct(string $excelFilePath)
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $this->PHPExcelObject = $reader->load($excelFilePath);
        $this->workSheet = $this->PHPExcelObject->getActiveSheet();
    }

    /**
     * Filter Translation
     * 
     * @param string $translation Translation
     * @return mixed Filtered data
     */
    protected abstract function filterTranslation($translation);

    /**
     * Filter Original
     * 
     * @param string $original Original
     * @return mixed Filtered data
     */
    protected abstract function filterOriginal($original);

    /**
     * Gets row from excel sheet
     * 
     * @param int $rowIndex Row index
     * @return array
     */
    protected function _getRow(int $rowIndex): array
    {
        $dataTmp = $this->workSheet->rangeToArray("A$rowIndex:B$rowIndex");
        $data = array_pop($dataTmp);
        return $data;
    }

    /**
     * Gets Original
     * 
     * @param int $rowIndex Row index
     * @return mixed Original data
     */
    protected function _getOriginal(int $rowIndex)
    {
        list($original, $translation) = $this->_getRow($rowIndex);
        return $this->filterOriginal($original);
    }

    /**
     * Is Original data empty?
     * 
     * @param int $rowIndex Row index
     * @return bool
     */
    protected function _hasEmptyOriginal(int $rowIndex)
    {
        return empty(trim(array_get($this->_getRow($rowIndex), 0)));
    }

    /**
     * Gets Translation
     * 
     * @param int $rowIndex Row index
     * @return mixed Translation data
     */
    protected function _getTranslation(int $rowIndex)
    {
        list($original, $translation) = $this->_getRow($rowIndex);
        return $this->filterTranslation($translation);
    }

    /**
     * Gets row by index
     * 
     * @param int $rowIndex Row index
     * @return array 
     */
    protected function getRow(int $rowIndex): array
    {
        return [$this->_getOriginal($rowIndex), $this->_getTranslation($rowIndex)];
    }

    /**
     * Gets current Original data
     * 
     * @return mixed
     */
    public function getOriginal()
    {
        list($original, $translation) = $this->currentRow;
        return $original;
    }

    /**
     * Gets current Translation data
     * 
     * @return mixed
     */
    public function getTranslation()
    {
        list($original, $translation) = $this->currentRow;
        return $translation;
    }

    /**
     * Gets number of rows
     * 
     * @return int
     */
    public function getRowNum(): int
    {
        return (int) $this->workSheet->getHighestRow();
    }

    /**
     * Iterates through data
     * 
     * @return array
     */
    public function iterate(): \Iterator
    {
        $this->lastRow = [];
        foreach ($this->workSheet->getRowIterator() as $rowIndex => $row)
        {
            if (1 == $rowIndex)
            {
                $this->lastRow = $this->getRow($rowIndex);
                continue;
            }

            if ($this->_hasEmptyOriginal($rowIndex))
            {
                $this->lastRow[1] .= self::PARAGRAPH_GLUE . $this->_getTranslation($rowIndex);
                continue;
            } else
            {
                $this->currentRow = $this->lastRow;
                $this->lastRow = $this->getRow($rowIndex);
                yield $this->currentRow;
            }
        }
        $this->currentRow = $this->lastRow;
        yield $this->currentRow;
    }

}
