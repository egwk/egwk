<?php

namespace App\EGWK;

use Illuminate\Support\Facades\DB;

class Hymnal
{

    /**
     * Get hymnals table
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function hymnals(): \Illuminate\Database\Query\Builder
    {
        $hymnals = DB::table('hymnal_book');
        return $hymnals;
    }

    /**
     * Translate verse
     *
     * @param string $lang
     * @param string $slug
     * @param string $no
     * @param string|null $verse
     * @return array
     */
    public function translate(string $lang, string $slug, string $no, string $verse = null): array
    {
        $table = DB::table('api_hymnal_synch')
            ->where('source_slug', $slug)
            ->where('source_hymn_no', $no)
            ->select(['slug', 'hymn_no', 'lang', 'title', 'hymn_uri']);
        if ('all' !== $lang) {
            $table->where('lang', $lang);
        }
        $data = $table
            ->get()
            ->toArray();
        foreach ($data as $key => $record) {
            $data[$key]->verses = $this->verse($record->slug, $record->hymn_no, $verse);
        }
        return $data;
    }

    /**
     * Get verse
     *
     * @param string $slug
     * @param string $no
     * @param string|null $verse
     * @return object
     */
    public function verse(string $slug, string $no, string $verse = null): object
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
