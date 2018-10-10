<?php

namespace App\Console\Commands\Install;

use \Illuminate\Console\Command;
use App\EGWK\Install\Writings;

class Download extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:writings {--s|skipto=} {--t|titleonly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloads Ellen White writings, and dumps books into csv file.';

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
        $skipto = $this->option('skipto');
        $bookTitlesOnly = $this->option('titleonly');

        // Setting up EGWWritings API
        $apiConsumer = new Writings\APIConsumer();
        $tokenStore = new Writings\APIConsumer\TokenStore\Redis();
        $request = new Writings\APIConsumer\Request($apiConsumer, $tokenStore);
        $iterator = new Writings\APIConsumer\Iterator($request);

        // Defining target file
        $outputFile = 'writings/egwwritings.csv';

        // Setting up text processing
        $morphy = new Writings\Morphy(storage_path('data/phpmorphy/en'));
        $filter = new Writings\Filter($morphy);
        // $store = new Writings\Store\File\CsvDump($filter, $outputFile, null == $skipto);
        $store = new Writings\Store\Database($filter);

        // Start download!
        $downloader = new Writings\Download($iterator, $store);
        null !== $skipto and $downloader->setSkipTo($skipto);
        $downloader->setBookTitlesOnly($bookTitlesOnly);
        $downloader->writings();
    }

}
