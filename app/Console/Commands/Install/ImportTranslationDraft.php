<?php

namespace App\Console\Commands\Install;

use Facades\ {
    App\EGWK\Synch
};
use \Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportTranslationDraft extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:draft {--f|file=} {--s|skipempty} {--x|noexport}';

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
     * Export draft to txt
     *
     * @param string $translationCode
     * @return void
     */
    protected function export(string $translationCode): void
    {
        $this->output->writeln('Creating backup first.');
        $this->call('export:draft', [
            '--file' => $translationCode
        ]);
    }

    /**
     * Import translation text file from storage/app/synch
     *
     * @param $translationCode
     * @param $translationFile
     * @param bool $skipEmpty
     * @throws \Throwable
     */
    public function import($translationCode, $translationFile, $skipEmpty = false)
    {
        Synch::cleanUp($translationCode);
        $translation = Synch::getTranslation($translationFile, $skipEmpty);
        $progress = new ProgressBar($this->output, count($translation));
        $progress->start();
        \DB::transaction(function () use ($progress, $translation, $translationCode) {
            foreach (array_values($translation) as $k => $row) {
                $progress->advance();
                Synch::addTranslation($translationCode, $k + 1, $row);
            }
        });
        $progress->finish();
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
        $export = !$this->option('noexport');
        $this->output->writeln($translationCode);
        if ($export) {
            $this->export($translationCode);
        }
        $this->import($translationCode, "$translationCode.txt", $skipEmpty);
        $this->output->newLine();
    }

}
