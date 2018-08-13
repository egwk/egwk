<?php

namespace App\EGWK\Install\Writings\Export;

use App\EGWK\Install\Writings\Filter;

/**
 * Dumps CSV data
 *
 * @author Peter
 */
class CsvDump extends File
{

    use \App\EGWK\Install\Writings\Tools\Csv;

    const MODIFIER_PARAGRAPHS = "paragraphs";
    const MODIFIER_WORDS = "words";
    const MODIFIER_SENTENCES = "sentences";

    protected $parents = [1 => '', '', '', '', '', ''];

    /**
     * Class constructor
     *
     * @access public
     * @param Filter $filter Filter
     * @param string $outputFile Output file
     * @return void
     */
    public function __construct(Filter $filter, $outputFile = "./data")
    {
        parent::__construct($filter, $outputFile);
    }

    /**
     * Clears parents
     *
     * @access protected
     * @param array $parents Parents
     * @param int $elementLevel Element level
     * @return StdClass JSON node
     */
    protected function clearParents(&$parents, $elementLevel)
    {
        for ($i = $elementLevel; $i <= 6; $i++)
        {
            $parents[$i] = '';
        }
    }

    /**
     * Get element level
     *
     * @access protected
     * @param string $elementType Element type
     * @return int Element level
     */
    protected function getElementLevel(string $elementType): int
    {
        if (preg_match('/h[1-6]/', $elementType))
        {
            return (int) substr($elementType, 1, 1);
        }
        return 7;
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
        $this->resetOutputFile(self::MODIFIER_PARAGRAPHS);
        $this->resetOutputFile(self::MODIFIER_WORDS);
        $this->resetOutputFile(self::MODIFIER_SENTENCES);
    }

    /**
     * Generates word set
     *
     * @access protected
     * @param StdClass $paragraph Paragraph
     * @return string Set of words
     */
    protected function words($paragraph)
    {
        $words = $this->chainFilter
                ->set($paragraph->content)
                ->strip()
                ->normalize()
                ->split()
                ->killStopWords()
                ->lemmatize()
                ->sort()
                ->stick()
                ->get();
        return $words;
    }

    /**
     * Generates list of sentences
     *
     * @access protected
     * @param StdClass $paragraph Paragraph
     * @return array List of sentences
     */
    protected function sentences($paragraph)
    {
        $sentences = $this->sentenceFilter->splitIntoSentences($this->filter->strip($paragraph->content));
        return $sentences;
    }

    /**
     * Generates word lists by sentence
     *
     * @access protected
     * @param array $sentences Sentences
     * @return array Word lists by sentence
     */
    protected function sentenceWordLists(array $sentences)
    {
        $sentenceWordLists = $this->sentenceChainFilter
                ->set($sentences)
                ->strip()
                ->normalize()
                ->split()
                ->killStopWords()
                ->lemmatize()
                ->sort()
                ->stick()
                ->get();
        return $sentenceWordLists;
    }

    /**
     * Processes and exports a paragraph to the CSV files
     *
     *    TRUNCATE TABLE egwk3_original;
     *    LOAD DATA LOCAL INFILE egwwritings.paragraphs.csv
	 *    INTO TABLE table_name CHARACTER SET UTF8 
     *        FIELDS TERMINATED BY '|' 
     *        LINES TERMINATED BY '@\n' 
     *        (
     *            para_id, id_prev, id_next,
	 *            refcode_1, refcode_2, refcode_3, refcode_4, refcode_short, refcode_long,
     *            element_type, element_subtype, content, puborder,
	 *            parent_1, parent_2, parent_3, parent_4, parent_5, parent_6, wordlist
     *        );
     *
     * @access public
     * @param StdClass $paragraph Paragraph
     * @return void
     */
    public function export($paragraph)
    {
        $words = $this->words($paragraph);
        $wordList = str_replace(' ', "\n", $words) . "\n";
        $this->writeOutputFile($wordList, self::MODIFIER_WORDS);
        $sentences = $this->sentences($paragraph);
        $sentenceWordLists = $this->sentenceWordLists($sentences);
        foreach ($sentences as $k => $sentence)
        {
            $sentenceWordList = array_get($sentenceWordLists, $k, "");
            $csvSentenceRow = $this->createCsv("|", "@\n", $paragraph->para_id, ($k + 1), $sentence, $sentenceWordList);
            $this->writeOutputFile($csvSentenceRow, self::MODIFIER_SENTENCES);
        }
        $level = $this->getElementLevel($paragraph->element_type);
        $this->clearParents($this->parents, $level);
        $csvRow = $this->createCsv("|", "@\n", $paragraph, $this->parents, $words);
        $this->writeOutputFile($csvRow, self::MODIFIER_PARAGRAPHS);
		if($level < 7)
		{
			$this->parents[$level] = $paragraph->para_id;
		}
    }

}
