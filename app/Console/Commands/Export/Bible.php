<?php

namespace App\Console\Commands\Export;

use Illuminate\Console\Command;

class Bible extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:bible {translations*} {--c|cleanup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Bible translation to plain txt format';

    /**
     * Should remove Strong numbers
     *
     * @var bool
     */
    protected $removeStrongs = false;

    /**
     * @var int
     */
    protected $current = 0;

    /**
     * @var bool
     */
    protected $resetFile = true;

    /**
     * Get translation by code
     *
     * @param string $translationCode
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTranslation(string $translationCode): \Illuminate\Database\Query\Builder
    {
        return \DB::table('bible_verse')
            ->join('bible_translation', 'bible_verse.translation_id', '=', 'bible_translation.id')
            ->select('book_id', 'chapter', 'verse', 'content')
            ->where('bible_translation.code', $translationCode)
            ->orderBy('book_id')
            ->orderBy('chapter')
            ->orderBy('verse')
//             ->limit(10) // todo: for testing only
            ;
    }

    /**
     * Cleanup Strong numbers
     *
     * @param string $content
     * @return string|string[]|null
     */
    protected function cleanupStrongs(string $content)
    {
        return preg_replace('/<S>[0-9]+<\/S>/', '', $content);
    }

    /**
     * Cleanup Strong numbers
     *
     * @param string $content
     * @return mixed|string|string[]|null
     */
    protected function cleanupVerse(string $content)
    {
        $content = $this->removeStrongs ? $this->cleanupStrongs($content) : $content;
        $content = preg_replace('/\ \ +/', " ", $content);
        $content = str_replace(["\r\n", "\r", "\n"], "\\n", $content);
        return $content;
    }

    protected function tick($current)
    {
        if ($current != $this->current) {
            $this->current = $current;
            $this->output->write(".");
        }
    }

    /**
     * Format single Bible verse row
     *
     * @param object $verse
     * @return string
     */
    public function formatVerse(object $verse): string
    {
        $this->tick($verse->book_id);
        $verse->content = $this->cleanupVerse($verse->content);
        return "$verse->book_id:$verse->chapter:$verse->verse\t$verse->content";
    }

    /**
     * Format Bible text for export
     *
     * @param $data
     * @return \Generator
     */
    protected function exportFormat($data): \Generator
    {
        yield $data
            ->map([$this, 'formatVerse'])
            ->implode("\n");
    }

    protected function cleanCode(string $translationCode): string
    {
        return $this->removeStrongs ? substr($translationCode, 0, -1) : $translationCode;
    }

    /**
     * Get Path
     *
     * @param string $translationCode
     * @return string
     */
    protected function getPath(string $translationCode): string
    {
        return "bible/$translationCode.txt";
    }

    protected function resetFile($path)
    {
        file_put_contents(\Storage::path($path), '');
    }

    /**
     * Loop on translations
     *
     * @param array $translations
     */
    protected function loopOnTranslations(array $translations, $cleanup): void
    {
        foreach ($translations as $translationCode) {
            $this->removeStrongs = false;
            if (ends_with($translationCode, '+')) {
                $this->removeStrongs = $cleanup;
            }
            $this->version = $this->cleanCode($translationCode);

            $path = $this->getPath($this->version);

            $this->info("\nExporting $translationCode to `$path`");

            if ($this->resetFile) {
                $this->resetFile($path);
            }
            foreach ($this->exportFormat(
                $this
                    ->getTranslation($translationCode)
                    ->get()
            ) as $data) {
                file_put_contents(\Storage::path($path), $data, FILE_APPEND);
            }
        }
        $this->info(" done.");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->resetFile = true;
        $translations = $this->argument('translations');
        $cleanup = $this->option('cleanup');
        $this->loopOnTranslations($translations, $cleanup);
    }
}
