<?php

namespace App\Console\Commands\Export\Bible\Ngrams;

use Foolz\SphinxQL\SphinxQL;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class FilterStopNgrams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ngrams:bible:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Filters Bible "stop n-grams"';

    protected $occurrenceLimit = 4;

    protected $bufferLimit = 1000;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $sphinx = sphinx();

        $inputFile = 'bible/ngrams.txt';
        $outputFile = 'bible/filtered-ngrams.txt';

        file_put_contents(\Storage::path($outputFile), '');

        $handle = fopen(\Storage::path($inputFile), "r");

        $progress = new ProgressBar($this->output, 52194266);

        // todo: optimize speed

        $buffer = "";
        $index = 0;

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                [$id, $version, $bookId, $chapter, $verse, $stopwords, $wordCountFiltered, $wordCount, $windowIndex, $wordLimit, $query] = explode("\t", $line);
                $queryResult = $sphinx
//                    ->select('version', 'book_id', 'chapter', 'verse')
                    ->select('version', 'book_id', 'chapter', 'verse', 'stopwords', 'word_count_filtered', 'word_count', 'window_index', 'word_limit')
                    ->from('i_bible_ngrams')
                    ->match('content', SphinxQL::expr('="^' . $query . '$"'))
                    ->where('version', $version)
                    ->where('id', '>', (int)$id)
                    ->limit($this->occurrenceLimit + 1)
                    ->execute();
                if ($queryResult->count() <= $this->occurrenceLimit) {
                    $buffer .= $line;
                    $index++;
                    if ($index >= $this->bufferLimit) {
                        file_put_contents(\Storage::path($outputFile), $buffer, FILE_APPEND);
                        $buffer = "";
                        $index = 0;
                    }
                }
                $progress->advance();
            }
            if ($buffer !== "") {
                file_put_contents(\Storage::path($outputFile), $buffer, FILE_APPEND);
            }
            fclose($handle);
        } else {
            // error opening the file.
        }
        $progress->finish();
    }
}
