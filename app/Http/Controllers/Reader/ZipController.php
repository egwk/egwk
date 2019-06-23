<?php

namespace App\Http\Controllers\Reader;

use App\Facades\Reader;
use App\Facades\ZipJson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZipController extends Controller
{
    public function book($code)
    {
        return response()->file(ZipJson::create($code, Reader::original($code)), ZipJson::header($code));
    }

    public function translation($code, $lang = null, $publisher = null, $year = null, $no = null)
    {
        return response()->file(ZipJson::create($code, Reader::translations($code, $lang, $publisher, $year, $no)), ZipJson::header($code));
    }

    public function paragraph($refcodeShort, $lang = null, $publisher = null, $year = null, $no = null)
    {
        $refcodeShort = Reader::filterCode($refcodeShort);
        return response()->file(ZipJson::create($refcodeShort, Reader::paragraph($refcodeShort, $lang, $publisher, $year, $no)), ZipJson::header($refcodeShort));
    }

    public function e4a($file)
    {
        $path = \Storage::path('e4a' . DIRECTORY_SEPARATOR . "$file.json");
        return response()->file(ZipJson::createFromFile($path), ZipJson::header($file));
    }

}
