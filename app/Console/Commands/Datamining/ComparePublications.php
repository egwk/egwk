<?php

namespace App\Console\Commands\Datamining;

use App\Jobs\ComparePublications as ComparePublicationsJob;
use Illuminate\Console\Command;
use Facades\App\EGWK\Datamining\ComparePublications as ComparePublicationsClass;
use Laravel\Horizon\Repositories\RedisJobRepository;
use Illuminate\Contracts\Redis\Factory as RedisFactory;

class ComparePublications extends Command
{

    protected $bookList;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compare:books {books*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare publications';

    /**
     * List jobs
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getJobs()
    {
        return collect(\Facades\Laravel\Horizon\Repositories\RedisJobRepository::getRecent())
            ->map(function ($item) {
                $collection = collect($item)
                    ->only(['id', 'connection', 'queue', 'name', 'status', 'payload', 'failed_at', 'completed_at', 'retried_by', 'reserved_at', 'index']);
                $payload = json_decode($collection->get('payload'));
                $payload->data->command = unserialize($payload->data->command);
                $array = $collection->toArray();
                $array['payload'] = $payload;
                return collect($array);
            });
    }

    /**
     * Get jobs from the same class in the queue by status
     *
     * @param \Illuminate\Support\Collection $jobs
     * @param string $status
     * @return \Illuminate\Support\Collection
     */
    protected function sameClassJobsInQueue(\Illuminate\Support\Collection $jobs, $status = null, $class = null)
    {
        $class = $class ?: get_class();
        return $jobs->filter(function ($item) use ($class, $status) {
            $classFound = $item->get('payload')->data->commandName == $class;
            if (null !== $status) {
                return $classFound && $item->get('status') == $status;
            }
            return $classFound;
        });
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->bookList = $this->argument('books');

        if (count($this->bookList) < 2) {
            $this->error("Give at least two publications!");
            exit(1);
        }

        $this->info('Comparing ' . implode(', ', $this->bookList));
        ComparePublicationsJob::dispatch($this->bookList)
            ->onConnection('redis');
        $this->info('Job dispatched.');
    }
}
