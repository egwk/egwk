<?php

namespace App\EGWK;


use App\Models\Tables\Original;
use Illuminate\Http\Request;

class Synch
{

    // TODO: make model for `translation_draft` and replace QueryBuilder queries where possible.

    public function synch($bookCode, $translationCode, $limit)
    {
        return [
            'original' => Original::where('refcode_1', $bookCode)
                ->orderBy('puborder', 'asc')
                ->paginate($limit)
            ,
            'translation' => \DB::table('translation_draft')
                ->select('content', 'seq')
                ->where('code', $translationCode)
                ->orderBy('seq')
                ->paginate($limit)
        ];

    }

    public function save($translationCode, $translation, $limit, $page)
    {
        // TODO: split functionality into shorter, separate methods
        $length = count($translation);
        $windowStart = $limit * ($page - 1) + 1;
        $windowEnd = $windowStart + $length - 1;
        $nextWindowDefaultStart = $windowStart + $limit;
        // $nextWindowActualStart = $windowStart + $length; // $windowEnd + 1
        \DB::table('translation_draft')
            ->whereBetween('seq', [$windowStart, $windowStart + $limit - 1])
            ->where('code', $translationCode)
            ->delete();
        if ($length != $limit) {
            $fullTableName = env('DB_TABLE_PREFIX', 'db_') . 'translation_draft';
            $query = "UPDATE $fullTableName JOIN " .
                "(SELECT @seq := $windowEnd) r " .
                "SET seq=@seq:=@seq+1 " .
                "WHERE seq >= $nextWindowDefaultStart AND code='$translationCode';";
            \DB::statement($query);
        }
        $rows = [];
        foreach ($translation as $k => $row) {
            $rows[] = [
                'code' => $translationCode,
                'seq' => ($k + $windowStart),
                'content' => $row ?: '',
            ];
        }
        \DB::table('translation_draft')->insert($rows);
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

    public function import($translationCode, $translationFile, $skipEmpty = false)
    {
        // TODO: add inserts as transactions
        // TODO: add proper logging
        \DB::table('translation_draft')
            ->where('code', $translationCode)
            ->delete();

        $translation = array_map(
            'trim',
            file(
                storage_path('app/synch/' . $translationFile)
            )
        );

        if ($skipEmpty) {
            $translation = array_filter($translation);
        }

        foreach (array_values($translation) as $k => $row) {
            echo "\r$k / " . count($translation);
            \DB::table('translation_draft')->insert(
                [
                    'code' => $translationCode,
                    'seq' => ($k + 1),
                    'content' => $row,
                ]
            );
        }
        echo "\n";

        return [
            'result' => 'success'
        ];
    }
}
