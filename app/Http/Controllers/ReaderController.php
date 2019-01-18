<?php

namespace App\Http\Controllers;

use App\Facades\Reader;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    public function books($lang = null)
    {
        $lang = $lang ?: $this->lang;
        $query = \DB::table('api_book')
            ->select('api_book.*');
        if (null !== $lang) {
            $query
                ->where('api_book.lang', $lang)
                ->join('collection_translation', function ($join) use ($lang) {
                    $join->on('collection_translation.text_id', '=', 'api_book.primary_collection_text_id')
                        ->where('collection_translation.lang', $lang);
                })
                ->select('api_book.*', 'collection_translation.translation AS collection_name');
        }
        $query
            ->orderBy('seq')
            ->orderBy('api_book.primary_collection_text_id')
            ->orderBy('church_approved', 'desc');
        return $query
            ->paginate($this->limit);
    }

    public function collections($lang = null)
    {
        $lang = $lang ?: $this->lang;
        $query = \DB::table('collection_translation')
            ->select('collection_translation.*')
            ->join('collection', 'collection_translation.text_id', '=', 'collection.text_id');
        if (null !== $lang) {
            $query
                ->where('collection_translation.lang', $lang);
        }
        $query
            ->orderBy('seq');
        return $query
            ->get();
    }

    public function book($code)
    {
        return Reader::original($code)
            ->paginate($this->limit);
    }

    public function toc($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        return Reader::toc($code, $lang, $publisher, $year, $no)
            ->paginate($this->limit);
    }

    public function chapter($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        return Reader::chapter($code, $lang, $publisher, $year, $no)
            ->paginate($this->limit);
    }

    public function translation($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        return Reader::translations($code, $lang, $publisher, $year, $no)
            ->paginate($this->limit);
    }

    public function parallel($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        return Reader::parallel($code, $lang, $publisher, $year, $no)
            ->paginate($this->limit);
    }

    public function paragraph($refcodeShort, $lang = null, $publisher = null, $year = null, $no = null)
    {
        return Reader::paragraph($refcodeShort, $lang, $publisher, $year, $no)
            ->paginate($this->limit);
    }

}
