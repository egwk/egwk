<?php

namespace App\Console\Commands\Install;

use App\Models\Tables\TranslationDraft;
use Illuminate\Console\Command;

class ExportTranslationDraft extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:draft {--f|file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translation draft into text file.';

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
        $this->output->writeln('Exporting: ' . $translationCode);

        $translation = TranslationDraft::select('content')
            ->where('code', $translationCode)
            ->orderBy('seq')
            ->get()
            ->pluck('content')
            ->map(function ($item) {
                return str_replace("\n", '<br/>', $item);
            })
            ->implode("\n");

        \Storage::put("synch/$translationCode.export.txt", $translation);
        $this->output->writeln('Exported to: ' . \Storage::path("synch/$translationCode.export.txt"));
        $this->output->newLine();

    }
}
