<?php

namespace App\EGWK\Install\Translations\Store;

use App\EGWK\Install\Exception\TranslationsException;
use App\EGWK\Install\Writings\Tools\Csv;

/**
 * Stores data in the Database
 *
 * @author Peter
 */
class File implements \App\EGWK\Install\Translations\Store
{

    use Csv;

    const TRANSLATION_METADATA_FILE = 'translation_metadata.csv';
    const TRANSLATION_FILE = 'translation.csv';


    public function __construct()
    {
        file_put_contents(storage_path(self::TRANSLATION_METADATA_FILE), '');
        file_put_contents(storage_path(self::TRANSLATION_FILE), '');
    }

    /**
     * Stores translation metadata
     *
     * @param \stdClass $metadata Translation metadata
     * @throws TranslationsException
     */
    public function metadata(\stdClass $metadata)
    {
        try {
            file_put_contents(storage_path(self::TRANSLATION_METADATA_FILE), $this->createCsv(((array)$metadata)), FILE_APPEND);
        } catch (\Exception $e) {
            throw new TranslationsException();
        }
    }

    /**
     * Stores translation metadata
     *
     * @param array $records Records
     * @throws TranslationsException
     */
    public function records(array $records)
    {
        try {
            file_put_contents(storage_path(self::TRANSLATION_FILE), $this->createCsv(((array)$records)), FILE_APPEND);
        } catch (\Exception $e) {
            throw new TranslationsException();
        }
    }

}
