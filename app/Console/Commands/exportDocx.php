<?php

namespace App\Console\Commands;

use App\Models\Tables\Publication;
use Facades\App\EGWK\Translation\CompileBook;
use Illuminate\Console\Command;

class exportDocx extends Command
{

    const COLLECTION_PREFIX = 'collection:';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:docx {books*} {--l|language=hu} {--t|threshold=70} {--m|multitranslation} {--p|publisher=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports book as Ms Word .docx';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get list of books
     *
     * @return array
     */
    protected function getBookList($books)
    {
        $first = reset($books);
        if (starts_with($first, self::COLLECTION_PREFIX)) {
            $collection = trim(str_replace(self::COLLECTION_PREFIX, '', $first));
            $tmp = Publication::query();
            if ('all' !== $collection) {
                $tmp->where('primary_collection_text_id', $collection);
            }
            $books = $tmp
                ->get(['book_code'])
                ->pluck('book_code')
                ->toArray();
        }
        return $books;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $books = $this->getBookList($this->argument('books'));
        $threshold = $this->option('threshold');
        $language = $this->option('language');
        $multiTranslation = $this->option('multitranslation');

        $this->info('Attempting compilation of ' . implode(', ',  $books));
        foreach ($books as $book) {
            $this->info("Compiling $book...");

            \Storage::put(
                "compilations/$book.json",
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

            $this->comment("$book done.");
        }
    }
}
