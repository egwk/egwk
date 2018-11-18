<?php

namespace App\EGWK;


use App\Models\Tables\Original;
use App\Models\Tables\TranslationDraft;

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
        $this->fullTableName = env('DB_TABLE_PREFIX', 'db_') . (new TranslationDraft())->getTable();
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
     * @param int $windowEnd
     * @param int $nextWindowDefaultStart
     * @param string $translationCode
     */
    protected function updateWindow(int $windowEnd, int $nextWindowDefaultStart, string $translationCode): void
    {

        $query = "UPDATE " . $this->fullTableName . " JOIN " .
            "(SELECT @seq := $windowEnd) r " .
            "SET seq=@seq:=@seq+1 " .
            "WHERE seq >= $nextWindowDefaultStart AND code='$translationCode';";
        \DB::statement($query);
    }

    /**
     * Insert draft segment rows into DB
     *
     * @param array $translation
     * @param int $windowStart
     * @param string $translationCode
     * @return array
     */
    protected function insertRows(array $translation, int $windowStart, string $translationCode): array
    {
        $rows = [];
        foreach ($translation as $k => $row) {
            $rows[] = [
                'code' => $translationCode,
                'seq' => ($k + $windowStart),
                'content' => $row ?: '',
            ];
        }
        TranslationDraft::insert($rows);
        return $rows;
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

        if ($length != $limit) {
            $this->updateWindow($windowEnd, $nextWindowDefaultStart, $translationCode);
        }

        $rows = $this->insertRows($translation, $windowStart, $translationCode);

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
            'insertions' => $rows,
            'deletions' => [$windowStart, $windowStart + $limit - 1],
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
                storage_path('app/synch/' . $translationFile)
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
