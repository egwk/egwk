<?php

namespace App\Console\Commands;

use Facades\App\EGWK\Translation\CompileBook;

class exportJson extends Export
{
    protected $signature = 'export:json' . self::SIGNATURE_SUFFFIX;
    protected $description = 'Exports book as JSON .json';

    /**
     * Export
     *
     * @return mixed
     */
    protected function export($book, $collection, $threshold = 70, $multiTranslation = false, $language = null)
    {
        $folder = 'compilations' . ($collection ? "/$collection" : '');
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
