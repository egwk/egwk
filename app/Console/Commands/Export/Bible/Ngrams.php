<?php

namespace App\Console\Commands\Export\Bible;

use App\Console\Commands\Export\Bible;
use App\EGWK\Install\Writings\Filter;
use App\EGWK\Install\Writings\Filter\Wrapper\Chain;
use App\EGWK\Install\Writings\Morphy;

class Ngrams extends Bible
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:bible:ngrams {translations*} {--t|minlegth=2} {--x|maxlength=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Bible translation n-grams';

    /**
     * @var Chain
     */
    protected $chainFilter;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var int
     */
    protected $minLegth;

    /**
     * @var int
     */
    protected $maxLength = 0;

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * @var string
     */
    protected $version = '';

    protected function baseWordList($content): Chain
    {
        return $this->chainFilter
            ->set($content)
            ->normalize()
            ->split()
            ->lemmatize();
    }

    protected function ngram(array $data, int $limit)
    {
        for ($i = 0; $i <= count($data) - $limit; $i++) {
            yield [$i, array_slice($data, $i, $limit)];
        }
    }

    protected function getNgramRow(bool $withStopwords, int $countFiltered, int $count, int $index, int $limit, string $ngram, $glue = "\t"): string
    {
        return implode($glue, [
            $withStopwords ? 'w' : 'n',
            $countFiltered,
            $count,
            $index,
            $limit,
            $ngram
        ]);
    }

    /**
     * Get n-grams
     *
     * @param string $content
     * @return array
     */
    protected function ngrams(array $data, int $minLength, int $maxLength): array
    {
        $result = [];
        for ($limit = $maxLength; $limit >= $minLength; $limit--) {
            foreach ($this->ngram($data, $limit) as [$index, $ngramArray]) {

                $filteredNgramArray = $this->filter->killStopWords($ngramArray);

                $count = count($ngramArray);
                $countFiltered = count($filteredNgramArray);

                $ngram = implode(' ', $ngramArray);
                $filteredNgram = implode(' ', $filteredNgramArray);

                // store n-grams non-existing in the verse only
                if (!isset($result[$ngram])) {
                    $result[$ngram] = $this->getNgramRow(true, $count, $count, $index, $limit, $ngram);
                    if ($countFiltered >= $minLength && !isset($result[$filteredNgram])) {
                        $result[$filteredNgram] = $this->getNgramRow(false, $countFiltered, $count, $index, $limit, $filteredNgram);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Format single Bible verse row
     *
     * @param object $verse
     * @return string
     */
    public function formatVerse(object $verse): string
    {
        $this->tick($verse->book_id);
        $base = $this->baseWordList(
            $this->cleanupVerse(
                $verse->content
            )
        );
        $maxLength = $this->maxLength ?: $base->length();
        $verses = [];
        foreach (
            $this->ngrams(
                $base->get(),
                $this->minLegth,
                $maxLength
            ) as $ngram
        ) {
            $verses[] = "$this->index\t$this->version\t$verse->book_id\t$verse->chapter\t$verse->verse\t$ngram";
            $this->index++;
        }
        return implode("\n", $verses);
    }


    /**
     * Format Bible text for export
     *
     * @param $data
     * @return \Generator
     */
    protected function exportFormat($data): \Generator
    {
        foreach ($data as $verse) {
            yield ($this->formatVerse($verse) . "\n");
        }
    }

    /**
     * Get Path
     *
     * @param string $translationCode
     * @return string
     */
    protected function getPath(string $translationCode): string
    {
        return "bible/ngrams.txt";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->index = 1;

        $morphy = new Morphy(storage_path('data/phpmorphy/en'));
        $this->filter = new Filter($morphy);
        $this->chainFilter = new Chain($this->filter);

        $this->minLegth = $this->option('minlegth');
        $this->maxLength = $this->option('maxlength');
        $translations = $this->argument('translations');

        $this->resetFile = false;
        $this->loopOnTranslations($translations, true);
    }
}
