<?php

namespace App\EGWK;

use Illuminate\Support\Facades\DB;

class Hymnal
{

    protected function whereValueOrList(&$query, $field, $value)
    {
        if (str_contains($value, ',')) {
            $values = explode(',', $value);
            $query->whereIn($field, $values);
        } elseif (strtolower($value) !== 'all') {
            $query->where($field, $value);
        } else {
            // 'all' => do nothing
        }
    }

    /**
     * Translate verse
     *
     * @param string $lang
     * @param string $slug
     * @param string $no
     * @param string|null $verses
     * @return array
     */
    public function translate(string $lang, string $slug, string $no, string $verses = null): array
    {
        $table = DB::table('api_hymnal_synch')
            ->where('source_slug', $slug)
            ->where('source_hymn_no', $no)
            ->select(['slug', 'hymn_no', 'lang', 'title', 'hymn_uri']);
        $this->whereValueOrList($table, 'lang', $lang);
        $data = $table
            ->get()
            ->toArray();
        foreach ($data as $key => $record) {
            $data[$key]->verses = $this->hymnVerses($record->slug, $record->hymn_no, $verses);
        }
        return [
            'original' => [
                'verses' => $this->hymnVerses($slug, $no, $verses),
            ],
            'translations' => $data
        ];
    }

    /**
     * Get hymn verses
     *
     * @param string $slug
     * @param string $no
     * @param string|null $verse
     * @return object|array
     */
    public function hymnVerses(string $slug, string $no, string $verse = null)
    {
        $table = DB::table('api_hymnal_verse')
            ->select(['verse_no', 'content', 'lily_hyphenated', 'note', 'verse_uri'])
            ->where('slug', $slug)
            ->where('hymn_no', $no);
        $data = $table
            ->get();

        if (empty($data->first()->verse_no)) {
            if (1 == $verse) {
                $array = collect($data->pop())->toArray();
                $array['last'] = "/hymn/$slug/$no/1";
                return $array;
            } elseif (null !== $verse) {
                return "";
            }
            return collect([1 => $data
                ->pop()]);
        }
        if (null !== $verse) {
            $array = collect($data
                ->where('verse_no', $verse)
                ->pop())->toArray();
            if ($verse > 1 && $verse <= $data->last()->verse_no) {
                $array['prev'] = "/hymn/$slug/$no/" . ($verse - 1);
            }
            if ($verse < $data->last()->verse_no) {
                $array['next'] = "/hymn/$slug/$no/" . ($verse + 1);
            }
            $array['last'] = "/hymn/$slug/$no/" . $data->last()->verse_no;
            return $array;
        }
        return $data;
//            ->keyBy('verse_no');
    }

}
