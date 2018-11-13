<?php

namespace App\Console\Commands\Install;

use \Illuminate\Console\Command;
use Facades\App\EGWK\Synch;

class ImportTranslationDraft extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:draft {--f|file=} {--s|skipempty}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports translation drafts into the EGWK database';

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
        $translationCode = $this->option('file');
        $skipEmpty = $this->option('skipempty');
        Synch::import($translationCode, "$translationCode.txt", $skipEmpty);
    }

}
