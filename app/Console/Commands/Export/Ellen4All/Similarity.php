<?php

namespace App\Console\Commands\Export\Ellen4All;

use App\Console\Commands\Export\Buffered;
use Symfony\Component\Console\Helper\ProgressBar;

class Similarity extends Buffered
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'e4a:similarity';

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
        $query = \DB::table('similarity_paragraph')
            ->select(['para_id1', 'w1', 'para_id2', 'w2'])
            ->where('w1', '>', 75)
            ->orWhere('w2', '>', 75);
        $this->exportQueryResults($query, 10000, 'similarity.json', 'e4a');
    }
}
