<?php

namespace App\Jobs;

use Facades\App\EGWK\Datamining\ComparePublications as ComparePublicationsClass;

class ComparePublications extends Job
{

    protected $books;
    protected $bookList;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bookList)
    {
        parent::__construct();
        $this->bookList = $bookList;
    }

    public function tags()
    {
        return ['compare', 'books:' . ComparePublicationsClass::getBookListTag($this->bookList)];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $path = ComparePublicationsClass::getFilePath($this->bookList);
        if ($this->areSameClassJobsInLimit(1, $this)) {
            \Log::info('Job\'s already running.');
            return;
        }

        if (!\Storage::exists($path)) {
            \Log::info('Job started: ' . implode(', ', $this->bookList));
            $json = ComparePublicationsClass::set($this->bookList)
                ->get()
                ->toJson(JSON_PRETTY_PRINT);
            \Storage::put($path, $json);
            \Log::info('Job done. Saved to ' . $path);
        } else {
            \Log::info('File already exists: ' . $path);
        }
    }
}
