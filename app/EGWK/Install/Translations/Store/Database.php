<?php

namespace App\EGWK\Install\Translations\Store;

use Illuminate\Support\Facades\DB;
use App\EGWK\Install\Exception\TranslationsException;

/**
 * Stores data in the Database
 *
 * @author Peter
 */
class Database implements \App\EGWK\Install\Translations\Store
{

    /**
     * Stores translation metadata
     * 
     * @param \stdClass $metadata Translation metadata
     * @throws TranslationsException
     */
    public function metadata(\stdClass $metadata)
    {
        try
        {
            DB::table('edition_work')->insert((array) $metadata);
        } catch (\Exception $e)
        {
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
        try
        {
            DB::table('translation_work')->insert($records);
        } catch (\Exception $e)
        {
            throw new TranslationsException();
        }
    }

}
