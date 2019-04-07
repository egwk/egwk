<?php

namespace App\EGWK\Install\Translations;

use App\EGWK\Install\Exception\TranslationsException;

/**
 * Metadata
 *
 * @author Peter
 */
class Metadata
{

    /**
     * Translations JSON file name
     */
    const TRANSLATIONS_JSON = 'translations.json';

    /**
     *
     * @var array Translations data
     */
    protected $translations = [];

    /**
     *
     * @var array Translations data keys
     */
    protected $translationsDataKeys = [];

    /**
     * Class constructor
     *  
     * @access public
     * @param string $lang Language code
     * @return void
     */
    public function __construct($lang = 'hu')
    {
        $this->translations = $this->readTranslationsFile(
            $this->getTranslationsFilePath($lang)
        );
    }

    /**
     * Returns publication ID
     * 
     * @param string $keyPrefix Metadata key prefix
     * @return string Publication ID
     */
    public function getPubID(string $keyPrefix): string
    {
        $metadata = $this->getMetadata($keyPrefix);
        return "$metadata->publisher_code:$metadata->year:$metadata->no";
    }

    /**
     * Returns translation metadata
     * 
     * @param string $keyPrefix Metadata key prefix
     * @return \stdClass Translation metadata
     * @throws Exception
     */
    public function getMetadata(string $keyPrefix): \stdClass
    {
        $matchingMetadata = preg_filter('/^(' . $keyPrefix . '.*)/', '$1', $this->translationsDataKeys);

        if (count($matchingMetadata) > 1)
        {
            throw new Exception("Ambiguous metadata for $keyPrefix.");
        } elseif (empty($matchingMetadata))
        {
            throw new Exception("Empty metadata for $keyPrefix.");
        }
        $key = array_shift($matchingMetadata);
        $metadata = array_get($this->translations, $key);
        return $metadata;
    }

    /**
     * Generates translations data keys
     * 
     * @param array $translationsData Translations data
     * @return array Translations data keys
     */
    protected function getTranslationKeys(array $translationsData): array
    {
        $this->translationsDataKeys = array_map(function($e)
        {
            return implode('_', [$e->book_code, $e->publisher_code, $e->year]);
        }, $translationsData);
        return $this->translationsDataKeys;
    }

    /**
     * Read translations File
     * 
     * @param string $translationsFilePath Translations JSON file path
     * @return array Translations data
     * @throws TranslationsException
     */
    protected function readTranslationsFile(string $translationsFilePath): array
    {
        try
        {
            $translationsFileContents = file_get_contents($translationsFilePath);
        } catch (\Exception $e)
        {
            throw new TranslationsException("Can't read translations metadata: $translationsFilePath");
        }

        $translationsData = \json_decode($translationsFileContents);

        if (!is_array($translationsData))
        {
            throw new TranslationsException("Invalid translations metadata: $translationsFilePath");
        }

        $translationsDataKeys = $this->getTranslationKeys($translationsData);
        $translations = array_combine($translationsDataKeys, $translationsData);
        return $translations;
    }

    /**
     * Returns translations JSON file path
     * 
     * @param string $lang Language code
     * @return string Translations JSON file path
     * @throws TranslationsException
     */
    protected function getTranslationsFilePath(string $lang = 'hu'): string
    {
        $translationsFilePath = \Storage::path("import/$lang/" . self::TRANSLATIONS_JSON);

        if (!file_exists($translationsFilePath))
        {
            throw new TranslationsException("Not found translations metadata for language: $lang");
        }
        return $translationsFilePath;
    }

}
