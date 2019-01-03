<?php

namespace App\Http\Controllers\Reader;

use App\Facades\Reader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MetadataController extends Controller
{
    public function toc($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        return response()->json(Reader::editionMetadata($code, $lang, $publisher, $year, $no)->get(), 200);
    }

    public function chapter($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        return response()->json(Reader::chapterMetadata($code, $lang, $publisher, $year, $no), 200);
    }

}
