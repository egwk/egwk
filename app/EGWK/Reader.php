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
        $sectionObjects = [];
        $bookObjects = [];
        $prevChapterObjects = [];
        $nextChapterObjects = [];

        $chapters = $this->commonFilter(DB::table('api_toc'), $lang, $publisher, $year, $no)
            ->where('para_id', $code)
            ->get();
        foreach ($chapters as $chapterObject) {
            if (null !== $chapterObject && isset($chapterObject->parent_1) && isset($chapterObject->parent_2)) {
                $sectionObjects[] = $this->commonFilter(DB::table('api_toc'), $chapterObject->lang, $chapterObject->publisher, $chapterObject->year, $chapterObject->no)
                    ->where('para_id', $chapterObject->parent_2)
                    ->first();
                $bookObjects[] = $this->commonFilter(DB::table('api_book'), $chapterObject->lang, $chapterObject->publisher, $chapterObject->year, $chapterObject->no)
                    ->where('book_code', $chapterObject->refcode_1)
                    ->first();
                $prevChapterObjects[] = $this->commonFilter(DB::table('api_toc'), $chapterObject->lang, $chapterObject->publisher, $chapterObject->year, $chapterObject->no)
                    ->where('refcode_1', $chapterObject->refcode_1)
                    ->where('puborder', '<', $chapterObject->puborder)
                    ->orderBy('puborder', 'desc')
                    ->first();
                $nextChapterObjects[] = $this->toc($chapterObject->refcode_1, $chapterObject->lang, $chapterObject->publisher, $chapterObject->year, $chapterObject->no)
                    ->where('puborder', '>', $chapterObject->puborder)
                    ->first();
            }
        }
        return (object)[
            'chapter' => $chapters,
            'nav' => (object)[
                'prev' => $prevChapterObjects,
                'next' => $nextChapterObjects,
            ],
            'section' => $sectionObjects,
            'book' => $bookObjects
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

    protected function bookOrArticle($code)
    {
        // SELECT * FROM `db_original` WHERE `refcode_short` LIKE 'ST July 17,%1901%' ORDER BY puborder
        $column = 'refcode_1';
        $eq = '=';
        if (preg_match('/\s+[0-9]{4}\s*$/', $code)) {
            $column = 'refcode_short';
            $eq = 'like';
            $code = preg_replace('/\s+([0-9]{4})/', '%$1%', $code);
        }
        return [$column, $eq, $code];
    }

    public function toc($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        [$column, $eq, $code] = $this->bookOrArticle($code);
        $chapters = $this->commonFilter(DB::table('api_toc'), $lang, $publisher, $year, $no)
            ->orderBy('puborder')
            ->where($column, $eq, $code);
        return $chapters;
    }

    public function parallel($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        [$column, $eq, $code] = $this->bookOrArticle($code);
        return $this
            ->contentQueryBase($lang, $publisher, $year, $no)
            ->where($column, $eq, $code);
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
        [$column, $eq, $code] = $this->bookOrArticle($code);
        return Original::where($column, $eq, $code)
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
