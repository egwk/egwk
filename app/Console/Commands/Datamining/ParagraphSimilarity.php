<?php

namespace App\Console\Commands\Datamining;

use Illuminate\Console\Command;
use Facades\App\EGWK\Datamining\ParagraphSimilarity as ParagraphSimilarityClass;

class ParagraphSimilarity extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'similarity:paragraph {--s|startid=0} {--l|limit=0} {--o|offset=0} {--f|output=ParagraphSimilarity.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Paragraph Similarity';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = $this->option('startid');
        $limit = $this->option('limit');
        $offset = $this->option('offset');
        $outputFileName = $this->option('output', 'ParagraphSimilarity.csv');

        ParagraphSimilarityClass::mine($start, $limit, $offset, $outputFileName);
    }
}
