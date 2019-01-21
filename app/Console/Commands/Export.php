<?php

namespace App\Console\Commands;

use App\Models\Tables\Publication;
use Illuminate\Console\Command;

abstract class Export extends Command
{
    const COLLECTION_PREFIX = 'collection:';
    const SIGNATURE_SUFFFIX = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function getCollectionPrefix($books)
    {
        $first = reset($books);
        if (starts_with($first, self::COLLECTION_PREFIX)) {
            return trim(str_replace(self::COLLECTION_PREFIX, '', $first));
        }
        return false;
    }

    /**
     * Get list of books
     *
     * @return array
     */
    protected function getBookList($books, $collection)
    {
        if ($collection) {
            $tmp = Publication::query();
            if ('all' !== $collection) {
                $tmp->where('primary_collection_text_id', $collection);
            }
            array_shift($books);
            $skip = $books;
            $books = $tmp
                ->get(['book_code'])
                ->pluck('book_code')
                ->reject(function ($value) use ($skip) {
                    return in_array($value, $skip);
                })
                ->toArray();
        }
        return $books;
    }

}
