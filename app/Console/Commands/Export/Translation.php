<?php

namespace App\Console\Commands\Export;

use App\Console\Commands\Export;

abstract class Translation extends Export
{

    const SIGNATURE_SUFFFIX = ' {books*} {--l|language=hu} {--o|original=translation} {--p|publisher=} {--y|year=} {--u|no=} {--i|ids}';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Export
     *
     * @return mixed
     */
    abstract protected function export($book, $collection, $language = 'hu', $original = 'translation', $publisher = null, $year = null, $no = null, $ids = false);


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

        $language = $this->option('language');
        $withOriginal = $this->option('original');
        $publisher = $this->option('publisher');
        $year = $this->option('year');
        $no = $this->option('no');
        $ids = $this->option('ids');

        $this->info('Books to export: ' . implode(', ', $books));

        foreach ($books as $book) {
            $this->info("Exporting $book...");

            $this->export(
                $book,
                $collection,
                $language,
                $withOriginal,
                $publisher,
                $year,
                $no,
                $ids
            );

            $this->comment("$book done.");
        }
    }

}
