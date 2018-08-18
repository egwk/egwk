<?php

namespace App\EGWK\Install\Writings\Store\File;

use App\EGWK\Install\Writings\Store\File;
use App\EGWK\Install\Writings\Filter;

/**
 * Dumps CSV data
 *
 * @author Peter
 */
class CsvDump extends File
{

    use \App\EGWK\Tools\Csv;

    const MODIFIER_PARAGRAPHS = "paragraphs";
    const MODIFIER_WORDS = "words";
    const MODIFIER_SENTENCES = "sentences";

    /**
     * Class constructor
     *
     * @access public
     * @param Filter $filter Filter
     * @param string $outputFile Output file
     * @return void
     */
    public function __construct(Filter $filter, $outputFile = "./data", $reset = true)
    {
        parent::__construct($filter, $outputFile, $reset);
    }

    /**
     * Initializes output file
     *
     * @access protected
     * @param string $outputFile Output file name
     * @return void
     */
    protected function initOutputFile(string $outputFile = "./data")
    {
        $this->outputFile = $outputFile;
        if ($this->reset) {
            $this->resetOutputFile(self::MODIFIER_PARAGRAPHS);
            $this->resetOutputFile(self::MODIFIER_WORDS);
            $this->resetOutputFile(self::MODIFIER_SENTENCES);

        }
    }

    /**
     * @inheritdoc
     */
    protected function storeWords($paragraph, $words)
    {
        $wordList = str_replace(' ', "\n", $words) . "\n";
        $this->writeOutputFile($wordList, self::MODIFIER_WORDS);
    }

    /**
     * @inheritdoc
     */
    protected function storeSentence($paragraph, $sentence, $sentenceWordList, $index)
    {
        $csvSentenceRow = $this->createCsv("|", "@\n", $paragraph->para_id, ($index + 1), $sentence, $sentenceWordList);
        $this->writeOutputFile($csvSentenceRow, self::MODIFIER_SENTENCES);
    }

    /**
     * @inheritdoc
     */
    protected function storeParagraph($paragraph, $sentences, $words)
    {
        $csvRow = $this->createCsv("|", "@\n", $paragraph, $this->parents, $words);
        $this->writeOutputFile($csvRow, self::MODIFIER_PARAGRAPHS);
    }

}
