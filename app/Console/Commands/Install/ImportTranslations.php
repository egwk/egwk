<?php

namespace App\Console\Commands\Install;

use \Illuminate\Console\Command;
use App\EGWK\Install\Translations;

class ImportTranslations extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:translations {--l|language=hu} {--f|filter=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports translations into the EGWK database';

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
        $lang = $this->option('language');
        $filter = $this->option('filter');
        $metadataService = new Translations\Metadata($lang);
        $insertService = new Translations\Store\Database(); // Alternatively: new Translations\Store\File();
        $dataType = Translations\DataFile\Excel\Filtered::class;
        $this->info("$lang, $dataType");
        (new Translations\Import($metadataService, $insertService, $lang, $dataType))->translations($filter);
    }

}
