<?php

namespace App\Console\Commands;

use App\Models\Tables\Publication;
use Illuminate\Console\Command;

abstract class Export extends Command
{
    const COLLECTION_PREFIX = 'collection:';
    const SIGNATURE_SUFFFIX = ' {books*} {--l|language=hu} {--t|threshold=70} {--m|multitranslation} {--p|publisher=}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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

    /**
     * Export
     *
     * @return mixed
     */
    abstract protected function export($book, $collection, $threshold = 70, $multiTranslation, $language);

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $booksParam = $this->argument('books');
        $collection = $this->getCollectionPrefix($booksParam);
        $books = $this->getBookList($booksParam, $collection);

        $threshold = $this->option('threshold');
        $multiTranslation = $this->option('multitranslation');
        $language = $this->option('language');

        $this->info('Attempting compilation of ' . implode(', ', $books));

        foreach ($books as $book) {
            $this->info("Compiling $book...");

            $this->export(
                $book,
                $collection,
                $threshold,
                $multiTranslation,
                $language
            );

            $this->comment("$book done.");
        }
    }
}
