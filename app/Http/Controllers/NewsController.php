<?php

namespace App\Http\Controllers;

use App\Models\Tables\Edition;
use App\Models\Tables\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected function baseQuery()
    {
        return News::orderBy('ndate', 'desc');
    }

    public function all(Request $request)
    {
        return
            [
                'pinned' => $this->pinned($request),
                'others' => $this->others($request),
                'books' => $this->books($request),
            ];
    }


    public function news(Request $request)
    {
        $limit = $request->get('limit', 5);
        $query = $this->baseQuery()
            ->take($limit);
        if ($request->has('language')) {
            $language = $request->get('language', 'hu');
            $query
                ->where('lang', $language);
        }
        return $query->get();
    }

    public function pinned(Request $request)
    {
        $limit = $request->get('limit', 5);
        $query = $this->baseQuery()
            ->where('important', '>', 0)
            ->take($limit);
        if ($request->has('language')) {
            $language = $request->get('language', 'hu');
            $query
                ->where('lang', $language);
        }
        return $query->get();
    }

    public function others(Request $request)
    {
        $limit = $request->get('limit', 5);
        $query = $this->baseQuery()
            ->where('important', 0)
            ->take($limit);
        if ($request->has('language')) {
            $language = $request->get('language', 'hu');
            $query
                ->where('lang', $language);
        }
        return $query->get();
    }

    public function books(Request $request)
    {
        $language = $request->get('language', 'hu');
        $limit = $request->get('limit', 5);
        $query = Edition::where('visible', 1)
            ->where('language', $language)
            ->orderBy('added', 'desc')
            ->take($limit);
        if ($request->has('summary')) {
            $query
                ->where('summary', '<>', '')
                ->whereNotNull('summary');
        }
        return $query->get();
    }
}
