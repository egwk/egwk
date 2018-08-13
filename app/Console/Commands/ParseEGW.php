<?php

namespace App\Console\Commands;

use App\EGWK\Parser\Reference\EGW;
use Illuminate\Console\Command;

class ParseEGW extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:egw {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filename = storage_path('data/' . $this->argument('filename'));
        file_put_contents("$filename.csv",
            (new EGW($filename))
                ->parse()
                ->getCSVParagraphs()
        );
    }
}
