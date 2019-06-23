<?php

namespace App\Console\Commands\Export;

use App\Console\Commands\Export;

abstract class Compile extends Export
{

    const SIGNATURE_SUFFFIX = ' {books*} {--l|language=hu} {--t|threshold=70} {--s|multisimilar} {--m|multitranslation} {--p|publisher=}';

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Export
     *
     * @return mixed
     */
    abstract protected function compile($book, $collection, $threshold = 70, $multiSimilar = false, $multiTranslation = false, $language = 'hu');


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
        $multiSimilar = $this->option('multisimilar');
        $multiTranslation = $this->option('multitranslation');
        $language = $this->option('language');

        $this->info('Attempting compilation of ' . implode(', ', $books));

        foreach ($books as $book) {
            $this->info("Compiling $book...");

            $this->compile(
                $book,
                $collection,
                $threshold,
                $multiSimilar,
                $multiTranslation,
                $language
            );

            $this->comment("$book done.");
        }
    }


}


