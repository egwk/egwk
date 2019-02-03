<?php

namespace App\Console\Commands\Export\Original;

use App\Console\Commands\Export\Original;

class Filtered extends Original
{
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
