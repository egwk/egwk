<?php

namespace App\Http\Controllers\Reader;

use App\Facades\Reader;
use App\Facades\Reader\SearchSimilar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->__get('query');
        return Reader::searchOriginal($query)
            ->paginate($this->limit);
    }

    public function translation(Request $request)
    {
        $query = $request->__get('query');
        return Reader::searchTranslation($query)
            ->paginate($this->limit);
    }

    public function similarity($paraId)
    {
        return SearchSimilar::similarParagraph($paraId)
            ->paginate($this->limit);
    }

    public function cluster(Request $request, $lang = null)
    {
        $query = $request->__get('query');
        $cover = $request->__get('cover');
        $covers = null == $cover ? $request->__get('covers') : $cover;
        $covered = null == $cover ? $request->__get('covered') : $cover;
        $reference = $request->__get('reference');
        return SearchSimilar::cluster($query, $covers, $covered, $reference, $lang)
            ->paginate($this->limit);
    }


}
