<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct()
    {
    }

    public function tags()
    {
        return ['job', 'params1'];
    }

    /**
     * List jobs
     *
     * @return \Illuminate\Support\Collection
     */
    public function getJobs()
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
     * @param string|null $status
     * @return \Illuminate\Support\Collection
     */
    public function sameClassJobsInQueue(\Illuminate\Support\Collection $jobs, $status = null, $class = null)
    {
        $jobId = null !== $this->job ? $this->job->getJobId() : null;
        $class = $class ?: get_class();
        return $jobs->filter(function ($item) use ($class, $status, $jobId) {
            $classFound = $item->get('payload')->data->commandName == $class;
            $notCurrentJob = $item->get('id') !== $jobId;
            $correctStatus = null !== $status ? $item->get('status') == $status : true;
            return $classFound && $notCurrentJob && $correctStatus;
        });
    }

    /**
     * Are running jobs from the same class under limit?
     *
     * @param int $limit
     * @param string|null $class
     * @return bool
     */
    public function areSameClassJobsInLimit($limit = 1, $class = null)
    {
        return $this->sameClassJobsInQueue($this->getJobs(), 'reserved', $class)->count() >= $limit;
    }


}
