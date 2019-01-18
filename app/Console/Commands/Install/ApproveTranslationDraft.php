<?php

namespace App\Console\Commands\Install;

use App\Models\Tables\Edition;
use Illuminate\Console\Command;
use Facades\ {
    App\EGWK\Synch
};

class ApproveTranslationDraft extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'approve:draft {--f|file=} {--c|cleanup} {--x|noexport} {--r|refreshcache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve translation draft by importing it to the live translation table';

    /**
     * Translation table
     *
     * @var string
     */
    protected $translationTable = 'translation';

    /**
     * Metadata fields
     *
     * @var array
     */
    protected $mandatoryMetadataFields = ['book_code', 'tr_code', 'tr_title', 'publisher_code', 'year', 'no', 'start_para_id', 'translator', 'language', 'text_id'];

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
     * Cleanup book translation
     *
     * @param string $halfRecord
     * @return void
     */
    protected function cleanup(array $halfRecord): void
    {
        \DB::table($this->translationTable)
            ->where($halfRecord)
            ->delete();
    }

    /**
     * Save metadata
     *
     * @param array $metadata
     * @return void
     */
    protected function saveMetadata(array $metadata): void
    {
        $edition = Edition::firstOrNew($metadata);
        $edition->save();
        $this->output->writeln('Metadata saved.');
    }

    /**
     * Load metadata
     *
     * @param string $translationCode
     * @return array
     */
    protected function getMetadata(string $translationCode): array
    {
        $this->output->writeln('Checking metadata...');

        $metadataFile = "synch/$translationCode.json";
        $metadata = null;

        if (!\Storage::exists($metadataFile)) {

            $this->output->warning("Metadata file not found. Trying database.");

            $bookCode = Synch::getBookCode($translationCode);
            try {
                $metadata = Edition::where('book_code', $bookCode)
                    ->firstOrFail()
                    ->toArray();
            } catch (\Exception $e) {
                $this->output->error("Metadata not found. Create $metadataFile first with relevant data.");
                exit(1);
            }
        } else {
            $metadata = json_decode(\Storage::get($metadataFile), true);
        }


        if (!array_has($metadata, $this->mandatoryMetadataFields)) {
            $this->output->error("Invalid Metadata, missing fields in $metadataFile.");
            exit(2);
        }

        $bookCode = array_get($metadata, 'book_code', '');

        $halfRecord = [
            'book_code' => $bookCode,
            'lang' => array_get($metadata, 'language', ''),
            'publisher' => array_get($metadata, 'publisher_code', ''),
            'year' => array_get($metadata, 'year', ''),
            'no' => array_get($metadata, 'no', ''),
        ];

        return [
            $bookCode,
            $metadata,
            $halfRecord
        ];
    }

    /**
     * Merge translation draft with original
     *
     * @param string $bookCode
     * @param string $translationCode
     * @param array $halfRecord
     * @return array
     */
    protected function merge(string $bookCode, string $translationCode, array $halfRecord): array
    {
        //
        // Joining translation draft with original:
        //
        // SELECT puborder, para_id, refcode_short,db_original.content, db_translation_draft.content as tr_content FROM db_original
        //    JOIN db_translation_draft ON db_translation_draft.seq = db_original.puborder
        //    WHERE db_translation_draft.code = '$translationCode'
        //        AND db_original.refcode_1 = '$metadata->book_code'
        //    ORDER BY puborder;
        //

        $this->output->writeln('Merging Translation with Original...');

        $merged = Synch::merge($translationCode, $bookCode)
            ->get()
            ->map(function ($item) use ($halfRecord) {
                return array_merge($halfRecord, [
                    'content' => $item->tr_content,
                    'para_id' => $item->para_id,
                ]);
            })
            ->toArray();
        return $merged;
    }

    /**
     * Saving translations
     *
     * @param array $merged
     */
    protected function saveTranslations(array $merged): void
    {
        $this->output->writeln('Inserting into Translation data table');
        try {
            \DB::table($this->translationTable)
                ->insert($merged);
        } catch (\PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->output->error("Translation already exists. Run with --cleanup.");
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $translationCode = $this->option('file');
        $cleanup = $this->option('cleanup');
        $export = !$this->option('noexport');
        $refreshcache = $this->option('refreshcache');

        $this->output->writeln('Approving: ' . $translationCode);

        if ($export) {
            $this->export($translationCode);
        }

        [$bookCode, $metadata, $halfRecord] = $this->getMetadata($translationCode);

        $this->saveMetadata($metadata);

        if ($cleanup) {
            $this->cleanup($halfRecord);
        }

        $merged = $this->merge($bookCode, $translationCode, $halfRecord);

        $this->saveTranslations($merged);

        if ($refreshcache) {
            $this->refreshcache();
        }

    }

    protected function refreshcache()
    {
        $this->call('migrate:rollback', [
            '--path' => '/database/migrations/api/'
        ]);
        $this->call('migrate', [
            '--path' => '/database/migrations/api/'
        ]);
    }
}
