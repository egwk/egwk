<?php

namespace App\EGWK\Install\Writings\Filter;

use App\EGWK\Install\Writings\Filter;

/**
 * WritingsSentenceFilter
 *
 * @author Peter
 */
class Sentence
{

    /**
     * Temporary separator replacement
     */
    const TEMP_SEPARATOR = '@';

    /**
     * Sentence separators
     */
    const SEPARATORS = '?.!';

    /**
     *
     * @var Filter Filter 
     */
    protected $filter;

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
    }

    /**
     * Splits text into sentences
     *
     * @access public
     * @param string $text Text
     * @return array Sentences
     */
    public function splitIntoSentences(string $text): array
    {
        $sentencesTmp = [];
        preg_match_all('/.*?[' . self::SEPARATORS . ']/s', $this->sentencePreFilters($text), $sentencesTmp);
        $sentences = array_shift($sentencesTmp);
        return $this->sentencePostFilters($sentences);
    }

    /**
     * Filters sentences
     *
     * @access public
     * @param array $sentences Sentences
     * @return array Filtered sentences
     */
    public function filterSentences(array $sentences, $filter): array
    {
        return array_map([get_parent_class($this), $filter], $sentences);
    }

    /**
     * Filter call magic method
     *
     * @access public
     * @param string $methodName Method name
     * @param array $args Arguments
     * @return array
     */
    public function __call($methodName, $args)
    {
        $sentences = array_get($args, 0);
        if (is_callable([$this->filter, $methodName]) && is_array($sentences))
        {
            return array_map([$this->filter, $methodName], $sentences);
        }
    }

    /**
     * Sentence flow
     *
     * @access public
     * @param array $sentences Sentences
     * @param array $flow Flow
     * @return array
     */
    public function sentenceFlow($sentences, array $flow): array
    {
        $result = [];
        foreach ($sentences as $text)
        {
            $result[] = $this->filter->flow($text, array_map(function($transformation)
                    {
                        return $transformation;
                    }, $flow));
        }
        return $result;
    }

    /**
     * Sentence pre-filtering
     *
     * @access protected
     * @param string $text Text
     * @return string
     */
    protected function sentencePreFilters(string $text): string
    {
        return preg_replace(// quote marks and parentheses
                '/[“"\[\]”\(\)]*/'
                , ''
                , preg_replace(// spaces
                        '/\ \ +/'
                        , ' '
                        , str_replace(// horizontal ellipsis
                                ['[...]', '...']
                                , ' '
                                , preg_replace(// single capital letters with a dot
                                        '/(\s[A-Z])\./'
                                        , '$1' . self::TEMP_SEPARATOR
                                        , $text
                                )
                        )
                )
        );
    }

    /**
     * Sentence post-filtering
     *
     * @access protected
     * @param array $sentences Text
     * @return array
     */
    protected function sentencePostFilters(array $sentences): array
    {
        return array_map(function($e)
        {
            return trim(str_replace(self::TEMP_SEPARATOR, '.', $e));
        }, $sentences);
    }

}
