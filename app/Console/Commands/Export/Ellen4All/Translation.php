<?php

namespace App\Console\Commands\Export\Ellen4All;

use App\Console\Commands\Export\Buffered;
use Symfony\Component\Console\Helper\ProgressBar;

class Translation extends Buffered
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'e4a:translation {--l|language=hu} {--o|original} {--b|book=} {--z|zip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export similarity database for Ellen4All';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $language = $this->option('language');
        $createZip = $this->option('zip');
        $original = $this->option('original');
        $book = $this->option('book');

        $query = \DB::table('translation')
            ->select(['translation.para_id', 'original.puborder', 'translation.book_code', 'translation.publisher', 'translation.year', 'translation.no', 'translation.content AS translation'])
            ->join('original', 'original.para_id', '=', 'translation.para_id')
            ->orderBy('translation.book_code')
            ->orderBy('translation.publisher')
            ->orderBy('translation.year')
            ->orderBy('translation.no')
            ->orderBy('original.puborder')
            ->where('lang', $language);

        $suffix = "";

        if ($original) {
            $suffix .= "-original";
            $query = $query->addSelect(['original.content AS original']);
        }

        if ($book) {
            $suffix .= "-" . $book;
            $query = $query->where('translation.book_code', $book);
        }


        $path = $this->exportQueryResults($query, 10000, "$language$suffix.json", 'e4a');

        if ($createZip) {
            $this->info("Creating $path.zip.");
            $zip = new \ZipArchive();
            if ($zip->open("$path.zip", \ZipArchive::CREATE) !== true) {
                return false;
            }
            $zip->addFile($path, basename($path));
            $zip->close();
        }

    }
}
