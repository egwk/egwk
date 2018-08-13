<?php

namespace App\EGWK\Install\Writings;

/**
 * Filter
 *
 * @author Peter
 */
class Filter
{

    /**
     *
     * @var Morphy Morphy object
     */
    protected $morphy = null;

    /**
     * Class constructor
     *
     * @param Morphy $morphy Morphy object
     * @return void
     */
    public function __construct(Morphy $morphy)
    {
        $this->morphy = $morphy;
    }

    /**
     * Removes any HTML tags
     *
     * @access public
     * @param string $text Text
     * @return string
     */
    public function strip(string $text): string
    {
        return trim(strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    /**
     * Normalizes text
     *
     * @access public
     * @param string $text Text
     * @return string
     */
    public function normalize(string $text): string
    {
        return trim(strtolower(preg_replace(['/[\W0-9_]+/', '/(\s)\s+/'], [' ', '$1'], $text)));
    }

    /**
     * Splits text into array
     *
     * @access public
     * @param string $text Text
     * @return array
     */
    public function split(string $text): array
    {
        return array_filter(array_map('trim', explode(' ', $text)));
    }

    /**
     * Sticks array of words together
     *
     * @access public
     * @param array $words Words
     * @return string
     */
    public function stick(array $words): string
    {
        return implode(' ', $words);
    }

    /**
     * Removes stop words
     *
     * @access public
     * @param array $words List of words
     * @return array Word list without stop words
     */
    public function killStopWords(array $words): array
    {
        return array_filter(array_map(function($word)
                {
                    $word = trim($word);
                    if (strlen($word) < 2)
                    {
                        return "";
                    }
                    return $word;
                }, array_diff($words, $this->getStopWords())));
    }

    /**
     * Lemmatizes words
     *
     * @access public
     * @param array $words List of words
     * @return array List of lemmatized words
     */
    public function lemmatize(array $words): array
    {
        return $this->morphy->lemmatize($words);
    }

    /**
     * Sorts word list
     *
     * @access public
     * @param array $words List of words
     * @return array List of words sorted
     */
    public function sort(array $words): array
    {
        sort($words);
        return array_unique($words);
    }

    /**
     * Gets Morphy object
     *
     * @access public
     * @return Morphy Morphy object
     */
    public function getMorphy(): Morphy
    {
        return $this->morphy;
    }

    /**
     * Gets stop word list
     *
     * @access public
     * @return array Stop word list
     */
    public function getStopWords(): array
    {
        return config('install.stopwords', []);
    }

    /**
     * Runs filter work flow
     *
     * @access public
     * @param string $text Text
     * @param array $flow Filter Work flow
     * @return string Processed text
     */
    public function flow(string $text, array $flow): string
    {
        foreach ($flow as $transformation)
        {
            if (is_callable([$this, $transformation]))
            {
                $text = $this->{$transformation}($text);
            }
        }
        return $text;
    }

}
