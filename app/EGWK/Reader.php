<?php

namespace App\EGWK;

use App\Models\Tables\CacheSearch;
use App\Models\Tables\Original,
    App\Models\Tables\Translation,
    Illuminate\Support\Facades\DB;

/**
 * Description of Reader
 *
 * @author Peter
 */
class Reader
{

    public function editionMetadata($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        $edition = $this->commonFilter(DB::table('api_book'), $lang, $publisher, $year, $no)
            ->where('book_code', $code);
        return $edition;
    }

    public function chapterMetadata($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        $sectionObject = null;
        $bookObject = null;
        $prevChapterObject = null;
        $nextChapterObject = null;

        $chapterObject = $this->commonFilter(DB::table('api_toc'), $lang, $publisher, $year, $no)
            ->where('para_id', $code)
            ->first();
        if (null !== $chapterObject && isset($chapterObject->parent_1) && isset($chapterObject->parent_2)) {
            $sectionObject = $this->commonFilter(DB::table('api_toc'), $lang, $publisher, $year, $no)
                ->where('para_id', $chapterObject->parent_2)
                ->first();
            $bookObject = $this->commonFilter(DB::table('api_book'), $lang, $publisher, $year, $no)
                ->where('book_code', $chapterObject->refcode_1)
                ->first();
            $prevChapterObject = $this->commonFilter(DB::table('api_toc'), $lang, $publisher, $year, $no)
                ->where('refcode_1', $chapterObject->refcode_1)
                ->where('puborder', '<', $chapterObject->puborder)
                ->orderBy('puborder', 'desc')
                ->first();
            $nextChapterObject = $this->toc($chapterObject->refcode_1, $lang, $publisher, $year, $no)
                ->where('puborder', '>', $chapterObject->puborder)
                ->first();
        }
        return (object)[
            'chapter' => $chapterObject,
            'nav' => (object)[
                'prev' => $prevChapterObject,
                'next' => $nextChapterObject,
            ],
            'section' => $sectionObject,
            'book' => $bookObject
        ];
    }

    public function chapter($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        $paragraphs = $this->commonFilter(DB::table('api_chapter'), $lang, $publisher, $year, $no)
            ->orderBy('puborder')
            ->where(function ($query) use ($code) {
                $query
                    ->where('parent_1', $code)
                    ->orWhere('parent_2', $code)
                    ->orWhere('parent_3', $code);
            });
        return $paragraphs;
    }

    public function toc($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        $chapters = $this->commonFilter(DB::table('api_toc'), $lang, $publisher, $year, $no)
            ->orderBy('puborder')
            ->where('refcode_1', $code);
        return $chapters;
    }

    public function parallel($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        return $this
            ->contentQueryBase($lang, $publisher, $year, $no)
            ->where('refcode_1', $code);
    }

    public function translations($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        return $this
            ->parallel($code, $lang, $publisher, $year, $no)
            ->has('translations');
    }

    public function paragraph($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        $code = $this->filterCode($code);
        $isParaID = $this->isParaID($code);
        list($column, $code) = $isParaID ? ['para_id', $code] : ['refcode_short', "$code%"];
        return $this
            ->contentQueryBase($lang, $publisher, $year, $no)
            ->where($column, 'like', $code);
    }

    public function original($code)
    {
        return Original::where('refcode_1', $code)
            ->orderBy('puborder', 'asc');
    }

    public function searchOriginal($query)
    {
        return CacheSearch::search($this->quotedPhraseQuery($query))
            ->orderBy('year', 'asc');
    }

    public function searchTranslation($query)
    {
        return Translation::search($this->quotedPhraseQuery($query));
    }

    public function quotedPhraseQuery($query)
    {
        return \Foolz\SphinxQL\SphinxQL::expr('="' . $query . '"');
    }

    public function filterCode($code)
    {
        return preg_replace('#(\s)\s+#', '$1', trim($code));
    }

    protected function isParaID($code)
    {
        return (bool)preg_match('#^[0-9]+\s*\.\s*[0-9]+$#', $code);
    }

    protected function contentQueryBase($lang = null, $publisher = null, $year = null, $no = null)
    {
        $self = $this;
        return Original::with(['translations' => function ($query) use ($self, $lang, $publisher, $year, $no) {
            $query = $this->commonFilter($query, $lang, $publisher, $year, $no);
        }])
            ->orderBy('puborder', 'asc');
    }

    public function commonFilter($table, $lang = null, $publisher = null, $year = null, $no = null)
    {
        !empty($lang) and $table->where('lang', $lang);
        !empty($publisher) and $table->where('publisher', $publisher);
        !empty($year) and $table->where('year', $year);
        !empty($no) and $table->where('no', $no);
        return $table;
    }

}
