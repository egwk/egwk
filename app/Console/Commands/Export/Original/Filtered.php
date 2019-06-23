<?php

namespace App\Console\Commands\Export\Original;

use App\Console\Commands\Export\Original;
use App\EGWK\Install\Writings\Filter;
use App\EGWK\Install\Writings\Filter\Wrapper\Chain;
use App\EGWK\Install\Writings\Morphy;
use App\Models\Tables\CacheSearch;
use Symfony\Component\Console\Helper\ProgressBar;

class Filtered extends Original
{

    const baseFolder = 'exported/';

    /**
     * @var Chain
     */
    protected $chainFilter;

    /**
     * @var Filter
     */
    protected $filter;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:original:filtered {books*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports original books into filtered TSV files';

    /**
     * @var int TSV row index
     */
    protected $index = 0;

    /**
     * @var int Buffer limit
     */
    protected $bufferLimit = 1024;

    /**
     * Apply filters to paragraph
     *
     * - normalization (lowercase alphanumerical characters only)
     * - with and without stopwords
     * - English lemmaziter
     *
     * @param string $content
     * @return array
     */
    protected function filter(string $content): array
    {
        $base = $this
            ->chainFilter
            ->set($content)
            ->strip()
            ->normalize();
        return [
            (clone $base)
                ->get(),
            (clone $base)
                ->split()
                ->lemmatize()
                ->stick()
                ->get(),
            $base
                ->split()
                ->killStopWords()
                ->lemmatize()
                ->stick()
                ->get()
        ];
    }

    protected function init()
    {
        $morphy = new Morphy(storage_path('data/phpmorphy/en'));
        $this->filter = new Filter($morphy);
        $this->chainFilter = new Chain($this->filter);
        $this->index = 1;
    }

    protected function getFileName(array $bookList): string
    {
        return implode('-', $bookList) . '-filtered.txt';
    }

    protected function getFilePath(array $bookList): string
    {
        return \Storage::path(static::baseFolder . $this->getFileName($bookList));
    }

    protected function query(array $bookList)
    {
        $query = CacheSearch::select(['para_id', 'refcode_short', 'content', 'stemmed_wordlist']);

        if (strtolower(array_get($bookList, 0)) !== 'all') {
            $query->whereIn('refcode_1', $bookList);
        }

        $query
            // ->limit(4000) // todo:for testing only
            // ->orderBy('primary_collection_text_id')
            ->orderBy('refcode_1')
            ->orderBy('puborder');
        return $query;
    }

    protected function loop(array $bookList, $startIndex = 0)
    {
        $q = $this->query($bookList);
        $progress = new ProgressBar($this->output, $q->count());
        $progress->start();

        yield implode("\t", ['index', 'para_id', 'refcode_short', 'content']) . "\n";

        foreach ($q->get() as $original) {
            $progress->advance();
            if ($this->index <= $startIndex) {
                continue;
            }
            yield implode("\t",
                    array_merge([
                            $this->index,
                            $original->para_id,
                            $original->refcode_short,
                            str_replace("\n", '', $original->content)
                        ]
                    //, $this->filter($original->content)
                    //, [$original->stemmed_wordlist]
                    )
                )
                . "\n";
            $this->index++;
        }
        $progress->finish();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->init();
        $bookList = $this->argument('books');
        $this->info('Filtering books: ' . implode(',', $bookList));
        $filePath = $this->getFilePath($bookList);
        $startIndex = 0;
        if (file_exists($filePath)) {
            // todo: read last line
            $lastLine = "425984\t821.7632\tin the beginning";
            [$startIndex,] = explode("\t", $lastLine, 2);
            $startIndex = 425984;
            $this->info('Skipping ' . $startIndex . ' records.');
        }
        $buffer = "";
        foreach ($this->loop($bookList, $startIndex) as $tsv) {
            $buffer .= $tsv;
            if ($this->index % $this->bufferLimit === 0) {
                file_put_contents($filePath, $buffer, FILE_APPEND);
                $buffer = "";
            }
        }
        file_put_contents($filePath, $buffer, FILE_APPEND);
        $this->info('done.');
    }
}
