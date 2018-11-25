<?php

namespace App\Http\Controllers;

use App\Facades\Hymnal;
use App\EGWK\Hymnal\Lily\Config as LilyConfig;
use Facades\ {
    App\EGWK\Hymnal\Lily
};
use DB;
use Illuminate\Http\Request;

class HymnalController extends Controller
{
    /**
     * Get Hymnal languages
     *
     * @return \Illuminate\Support\Collection
     */
    public function languages(): \Illuminate\Support\Collection
    {
        return DB::table('hymnal_book')
            ->distinct()->pluck('lang');
    }

    /**
     * Get Hymnal table
     *
     * @param string|null $lang
     * @return \Illuminate\Support\Collection
     */
    public function hymnals(string $lang = null): \Illuminate\Support\Collection
    {
        $table = DB::table('api_hymnal_book');
        if (null !== $lang) {
            $table
                ->where('lang', $lang);
        }
        return $table
            ->get();
    }

    /**
     * Get Hymnal ToC
     *
     * @param string $slug
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function hymnalToc(string $slug, int $limit): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return DB::table('api_hymnal_song')
            ->where('slug', $slug)
            ->paginate($limit);
    }

    /**
     * Get Hymnal metadata
     *
     * @param string $slug
     * @param string $no
     * @return \Illuminate\Support\Collection
     */
    public function hymnalEntry(string $slug, string $no): \Illuminate\Support\Collection
    {
        return DB::table('api_hymnal_song')
            ->where('slug', $slug)
            ->where('hymn_no', $no)
            ->get();
    }

    public function hymnVerses($slug, $no, $verse = null)
    {
        return Hymnal::hymnVerses($slug, $no, $verse);
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
        return Hymnal::translate($lang, $slug, $no, $verses);

    }

    /**
     * Translate verse
     *
     * @param string $lang
     * @param string $slug
     * @param string $no
     * @param string|null $verse
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function score(Request $request, string $slug, string $no, string $verses = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // /hymn/score/hitunk-enekei/1/1?type=STB&format=png&size=normal&piano=0&minifySoprano=1&header=0
        // '/score/{type}/{slug}/{no}/{verse}/
        // ?type=[type]&format=[format]&size=[size]
        // &piano=[piano]&minifySoprano=[minifySoprano]
        // &header=[header]&cache=[cache]'
        // $type, $slug, $no, $verse = null;

        $verses = empty($verses) ? null : $verses;
        Lily::setup(new LilyConfig([
            'slug' => $slug,
            'no' => $no,
            'verses' => $verses,
            'type' => $request->get('type', 'SATB'),
            'format' => $request->get('format', 'png'),
            'size' => $request->get('size', 'normal'),
            'pianoReduction' => $request->get('piano', false),
            'minifySoprano' => $request->get('minifySoprano', false),
            'header' => $request->get('header', true),
            'cache' => $request->get('cache', true),
        ]));
        return \Response::stream(function () {
            echo Lily::get();
        }, 200, ['content-type' => Lily::contentType()]);
    }

}
