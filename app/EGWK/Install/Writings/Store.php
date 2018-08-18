<?php

namespace App\EGWK\Install\Writings;

/**
 * Store
 *
 * @author Peter
 */
abstract class Store
{
    /**
     * @var array Parents
     */
    protected $parents = [1 => '', '', '', '', '', ''];

    /**
     * @var int Current Element Level
     */
    protected $level = 0;

    /**
     * @var array Inserted paragraph IDs
     */
    protected $inserted = [];

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
     * Begin store process
     *
     * @access public
     * @return void
     */
    public abstract function begin();

    /**
     * End store process
     *
     * @access public
     * @return void
     */
    public abstract function end();

    /**
     * Before storing a single paragraph
     *
     * @access protected
     * @return void
     */
    protected abstract function before();

    /**
     * After storing a single paragraph
     *
     * @access protected
     * @return void
     */
    protected abstract function after();

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
        for ($i = $elementLevel; $i <= 6; $i++) {
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
        if (preg_match('/h[1-6]/', $elementType)) {
            return (int)substr($elementType, 1, 1);
        }
        return 7;
    }

    /**
     * Get current element level
     *
     * @access protected
     * @return int Level
     */
    protected function getCurrentLevel(): int
    {
        return $this->level;
    }

    /**
     * Adds parent to the list
     *
     * @access protected
     * @param string $paraId Paragraph ID
     * @return void
     */
    protected function addParent(string $paraId): void
    {
        if ($this->level < 7) {
            $this->parents[$this->level] = $paraId;
        }

    }

    /**
     * Get element level
     *
     * @access protected
     * @param string $elementType Element type
     * @return void
     */
    protected function updateElementLevel(string $elementType): void
    {
        $this->level = $this->getElementLevel($elementType);
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
     * Store words
     *
     * @param object $paragraph
     * @param string $words
     * @return void
     */
    protected abstract function storeWords($paragraph, $words);

    /**
     * @param object $paragraph
     * @param string $sentence
     * @param string $sentenceWordList
     * @param int $index
     * @return void
     */
    protected abstract function storeSentence($paragraph, $sentence, $sentenceWordList, $index);

    /**
     * @param object $paragraph
     * @param array $sentences
     * @param string $words
     * @return void
     */
    protected abstract function storeParagraph($paragraph, $sentences, $words);

    /**
     * Stores a single paragraph
     *
     * @access public
     * @param mixed $paragraph Paragraph text or object
     * @return void
     */
    public function store($paragraph)
    {
        if (isset($this->inserted[$paragraph->para_id])) {
            return;
        }
        $this->inserted[$paragraph->para_id] = 1;

        $this->before();

        $words = $this->words($paragraph);
        $this->storeWords($paragraph, $words);
        $sentences = $this->sentences($paragraph);
        $sentenceWordLists = $this->sentenceWordLists($sentences);
        foreach ($sentences as $k => $sentence) {
            $sentenceWordList = array_get($sentenceWordLists, $k, "");
            $this->storeSentence($paragraph, $sentence, $sentenceWordList, $k);
        }

        $this->updateElementLevel($paragraph->element_type);
        $this->clearParents($this->parents, $this->getCurrentLevel());

        $this->storeParagraph($paragraph, $sentences, $words);

        $this->addParent($paragraph->para_id);

        $this->after();
    }

}
