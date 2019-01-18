<?php

namespace App\Console\Commands\Install;

use App\Models\Tables\TranslationDraft;
use Illuminate\Console\Command;
use Facades\ {
    App\EGWK\Synch
};

class ExportTranslationDraft extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:draft {--f|file=} {--p|parallel}';

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
        $parallel = $this->option('parallel');
        $this->output->writeln('Exporting: ' . $translationCode);

        $translation = "";

        if ($parallel) {
            $translation = Synch::merge($translationCode)
                ->get()
                ->map(function ($item) {
                    return
                        $item->content
                        //strip_tags($item->content)
                    . "\t"
                    . str_replace("\n", '<br/>', $item->tr_content);
                })
                ->implode("\n");
        } else {
            $translation = TranslationDraft::select('content')
                ->where('code', $translationCode)
                ->orderBy('seq')
                ->get()
                ->pluck('content')
                ->map(function ($item) {
                    return str_replace("\n", '<br/>', $item);
                })
                ->implode("\n");
        }

        $outoutFile = "synch/$translationCode.export" . ($parallel ? '.parallel' : '') . '.txt';
        \Storage::put($outoutFile, $translation);
        $this->output->writeln('Exported to: ' . \Storage::path($outoutFile));
        $this->output->newLine();

    }
}
