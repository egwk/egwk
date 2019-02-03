<?php

namespace App\Console\Commands\Export;

use Illuminate\Console\Command;

class Original extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:original {books*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports original language book as text .txt';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('export:txt', [
            'books' => $this->argument('books'),
            '--original' => 'original'
        ]);
    }
}
