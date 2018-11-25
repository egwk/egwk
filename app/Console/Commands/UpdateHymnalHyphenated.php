<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateHymnalHyphenated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:hymnal:hyphenated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates hyphenated verse lyrics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function loadLyrics()
    {
        return include storage_path('data/hymnal/lily_hyphenated.php');
    }

    /**
     * Updates hyphenated verse lyrics
     * Works with Hungarian "Hitünk Énekei" hymnal only so far.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        // $magnifyStaff
        $lyrics = $this->loadLyrics();
        \DB::transaction(function () use ($lyrics) {
            foreach ($lyrics as $sno => $lyric) {
                foreach ($lyric as $vno => $verse) {
                    \DB::table('hymnal_verse')
                        ->where('hymnal_id', 2)
                        ->where('hymn_no', $sno)
                        ->where('verse_no', $vno)
                        ->update(['lily_hyphenated' => $verse]);
                }
            }
        });
    }

    /**
     * Load score metadata from JSON
     *
     * @deprecated
     * @return array
     */
    protected function loadMetadata(): array
    {
        $metadata = [];
        $metadataTmp = json_decode(file_get_contents(storage_path('data/hymnal/lily_metadata.json')), true);
        foreach ($metadataTmp as $key => $item) {
            $metadata[$item['hymn_no']] = $item;
        }
        return $metadata;
    }

    /**
     * Execute the console command.
     *
     * @deprecated
     * @return mixed
     * @throws \Throwable
     */
    protected function updateScoreMetadata()
    {
        $metadata = $this->loadMetadata();
        $songs = \DB::table('api_hymnal_song')
            ->select()
            ->where('slug', 'hitunk-enekei')
            ->get('lily_score');
        \DB::transaction(function () use ($metadata, $songs) {
            foreach ($songs as $song) {
                $lilyScore = json_decode($song->lily_score, true);
                if (is_array($lilyScore)) {
                    $score = array_merge(
                        $lilyScore,
                        array_only(array_get($metadata, $song->hymn_no), ['key', 'time', 'partial'])
                    );
                    $lilyScoreJson = json_encode($score);

                    \DB::table('hymnal_song')
                        ->where('id', $song->id)
                        ->update(['lily_score' => $lilyScoreJson]);
                } else {
                    continue;
                }
            }
        });
    }
}
