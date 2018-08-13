<?php

namespace App\EGWK\Install\Writings;

use phpMorphy,
    phpMorphy_FilesBundle;

/**
 * Wrapper class for Morphy - Word lemmatizer
 *
 * @author Peter
 */
class Morphy
{

    /**
     *
     * @var phpMorphy PHPMorphy object 
     */
    protected $morphy = null;

    /**
     *
     * @var string Dictionary path
     */
    protected $dictPath = null;

    /**
     * Class constructor
     *
     * @access public
     * @param string $dictPath Dictionary path
     * @return void
     */
    public function __construct(string $dictPath = "./")
    {
        $this->dictPath = $dictPath;
    }

    /**
     * Lemmatizes word list
     *
     * @access public
     * @param array $words Word list
     * @return array Lemmatized word list
     */
    public function lemmatize(array $words): array
    {
        $result = [];
        foreach ($words as $word)
        {
            $result[] = $this->lemmatizeWord($word);
        }
        return $result;
    }

    /**
     * Lemmatizes a single word
     *
     * @access public
     * @param string $word Word
     * @return string Lemmatized word
     */
    public function lemmatizeWord(string $word): string
    {
        $tmp = $this->getMorphy()->lemmatize(strtoupper($word), phpMorphy::NORMAL);
        return strtolower($tmp !== false ? array_pop($tmp) : $word);
    }

    /**
     * Instantiates PHPMorphy object
     *
     * @access protected
     * @return phpMorphy PHPMorphy object
     */
    protected function getMorphy()
    {
        if (null === $this->morphy)
        {
            $dictBundle = new phpMorphy_FilesBundle($this->dictPath, 'eng');
            try
            {
                $this->morphy = new phpMorphy($dictBundle, [
                    'storage' => phpMorphy::STORAGE_FILE,
                    'with_gramtab' => false,
                    'predict_by_suffix' => true,
                    'predict_by_db' => true
                ]);
            } catch (phpMorphy_Exception $e)
            {
                die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
            }
        }
        return $this->morphy;
    }

}
