<?php

namespace App\EGWK;


use App\Models\Tables\Original;
use App\Models\Tables\TranslationDraft;
use Illuminate\Support\Facades\Storage;

class Synch
{

    /**
     * @var string Full table name (with DB prefix)
     */
    protected $fullTableName;

    /**
     * Synch constructor
     */
    public function __construct()
    {
        $this->fullTableName = config('database.connections.' . config('database.default') . '.prefix') . (new TranslationDraft())->getTable();
    }

    /**
     * Clean up (delete) items in selected window
     *
     * @param int $windowStart
     * @param int $limit
     * @param string $translationCode
     * @throws \Throwable
     */
    protected function cleanUpWindow(int $windowStart, int $limit, string $translationCode): void
    {
        TranslationDraft::whereBetween('seq', [$windowStart, $windowStart + $limit - 1])
            ->where('code', $translationCode)
            ->delete();
    }

    /**
     * Update translation draft segment sequence numbers
     *
     * @param int $windowStart
     * @param int $limit
     * @param int $length
     * @param int $nextWindowDefaultStart
     * @param string $translationCode
     */
    protected function updateWindow(int $windowStart, int $limit, int $length, string $translationCode)
    {
        TranslationDraft::where('seq', '>=', $windowStart + $limit)
            ->where('code', $translationCode)
            ->update(['seq' => \DB::raw('`seq` + ' . ($length - $limit))]);
    }

    /**
     * Insert draft segment rows into DB
     *
     * @param array $translations
     * @param int $windowStart
     * @param string $translationCode
     * @return array
     */
    protected function insertRows(array $translations, int $windowStart, string $translationCode): array
    {
        $records = [];
        foreach (array_values($translations) as $k => $translation) {
            $records[] = [
                'code' => $translationCode,
                'seq' => ($k + $windowStart),
                'content' => $translation ?: '',
            ];
        }
        TranslationDraft::insert($records);
        return $records;
    }

    /**
     * Save draft segment to DB
     *
     * @param string $translationCode
     * @param array $translation
     * @param int $limit
     * @param int $page
     * @return array
     * @throws \Throwable
     */
    public function save(string $translationCode, array $translation, int $limit, int $page)
    {
        $length = count($translation);
        $windowStart = $limit * ($page - 1) + 1;
        $windowEnd = $windowStart + $length - 1;
        $nextWindowDefaultStart = $windowStart + $limit;
        // $nextWindowActualStart = $windowStart + $length; // $windowEnd + 1

        $this->cleanUpWindow($windowStart, $limit, $translationCode);

        $updateWindow = "";

        if ($length != $limit) {
            $updateWindow = $this->updateWindow($windowStart, $limit, $length, $translationCode);
        }

        $records = $this->insertRows($translation, $windowStart, $translationCode);

        return [
            'result' => 'success',
            'translationCode' => $translationCode,
            'length' => $length,
            'limit' => $limit,
            'page' => $page,
            'windowStart' => $windowStart,
            'windowEnd' => $windowEnd,
            'nextWindowDefaultStart' => $nextWindowDefaultStart,
            'nextWindowActualStart' => $windowEnd + 1,
            'insertions' => $records,
            'deletions' => [$windowStart, $windowStart + $limit - 1],
            'updateWindow' => $updateWindow,
        ];
    }

    /**
     * Clean up translation
     *
     * @param string $translationCode
     * @throws \Throwable
     */
    public function cleanUp(string $translationCode): void
    {
        TranslationDraft::where('code', $translationCode)->delete();
    }

    /**
     * Read translation file
     *
     * @param string $translationFile
     * @param bool $skipEmpty
     * @return array
     */
    public function getTranslation(string $translationFile, bool $skipEmpty = false): array
    {
        $translation = array_map(
            'trim',
            file(
                Storage::path('synch/' . $translationFile)
            )
        );
        if ($skipEmpty) {
            return array_filter($translation);
        }
        return $translation;
    }

    /**
     * Insert single translation record
     *
     * @param string $translationCode
     * @param int $seq
     * @param string $content
     */
    public function addTranslation(string $translationCode, int $seq, string $content): void
    {
        TranslationDraft::insert(
            [
                'code' => $translationCode,
                'seq' => $seq,
                'content' => $content,
            ]
        );
    }

}
