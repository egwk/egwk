<?php

namespace App\Console\Commands\Export\Compilation;

use App\Console\Commands\Export\Compile;
use Facades\App\EGWK\Translation\CompileBook;

class Json extends Compile
{
    const baseFolder = 'compilations';

    protected $signature = 'compile:json' . self::SIGNATURE_SUFFFIX;
    protected $description = 'Compiles book as JSON .json';

    /**
     * Export
     *
     * @return mixed
     */
    protected function compile($book, $collection, $threshold = 70, $multiTranslation = false, $language = null)
    {
        $folder = static::baseFolder . ($collection ? "/$collection" : '');
        \Storage::put(
            "$folder/$book.json",
            json_encode(
                CompileBook::translate(
                    $book,
                    $threshold,
                    $multiTranslation,
                    $language
                ),
                JSON_PRETTY_PRINT
            )
        );
    }
}
