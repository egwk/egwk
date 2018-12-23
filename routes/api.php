<?php

use App\Facades\ChurchFields\HUC\Hymnal;
use App\Facades\ChurchFields\HUC\SabbathSchool;
use App\Facades\ZipJson;
use Facades\
{
    App\EGWK\Hymnal\Lily
};
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$perPage = \Request::get('per_page') ?: env('API_PAGINATION', 25);

Route::get('/', function (Request $request) {
    return config('egwk.api_help');
});

Route::fallback(function () {
    return response()->json(['message' => 'Not Found.'], 404);
})->name('api.fallback.404');

/*
|--------------------------------------------------------------------------
| Routes for EGWK modules
|--------------------------------------------------------------------------
*/

//
// Reader
//
Route::group(['prefix' => 'reader',], function () use ($perPage) {
    Route::get('/', function (Request $request) {
        return config('egwk.api_help.entries.reader');
    });

        Route::get('/books/{lang?}', function ($lang = null) use ($perPage) {
            $table = DB::table('api_book');
            if (null !== $lang) {
                $table->where('lang', $lang);
            }
//         ->orderBy('primary_collection_text_id')
//         ->orderBy('church_approved', 'desc')
            return $table->paginate($perPage);
        });

    Route::get('/book/{code}', function ($code) use ($perPage) {
        return Reader::original($code)
            ->paginate($perPage);
    });

    Route::get('/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}', function ($code, $lang = null, $publisher = null, $year = null, $no = null) use ($perPage) {
        return Reader::toc($code, $lang, $publisher, $year, $no)
            ->paginate($perPage);
    });

    Route::get('/chapter/{code}/{lang?}/{publisher?}/{year?}/{no?}', function ($code, $lang = null, $publisher = null, $year = null, $no = null) use ($perPage) {
        return Reader::chapter($code, $lang, $publisher, $year, $no)
            ->paginate($perPage);
    });

//
// Reader / Metadata
//
    Route::group(['prefix' => 'metadata',], function () use ($perPage) {
        Route::get('/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}', function ($code, $lang = null, $publisher = null, $year = null, $no = null) use ($perPage) {
            return response()->json(Reader::editionMetadata($code, $lang, $publisher, $year, $no)->first(), 200);
        });

        Route::get('/chapter/{code}/{lang?}/{publisher?}/{year?}/{no?}', function ($code, $lang = null, $publisher = null, $year = null, $no = null) use ($perPage) {
            return response()->json(Reader::chapterMetadata($code, $lang, $publisher, $year, $no), 200);
        });
    });

    Route::get('/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}', function ($code, $lang = null, $publisher = null, $year = null, $no = null) use ($perPage) {
        return Reader::translations($code, $lang, $publisher, $year, $no)
            ->paginate($perPage);
    });

    Route::get('/parallel/{code}/{lang?}/{publisher?}/{year?}/{no?}', function ($code, $lang = null, $publisher = null, $year = null, $no = null) use ($perPage) {
        return Reader::parallel($code, $lang, $publisher, $year, $no)
            ->paginate($perPage);
    });

    Route::get('/paragraph/{refcode_short}/{lang?}/{publisher?}/{year?}/{no?}', function ($refcodeShort, $lang = null, $publisher = null, $year = null, $no = null) use ($perPage) {
        return Reader::paragraph($refcodeShort, $lang, $publisher, $year, $no)
            ->paginate($perPage);
    });

//
// Reader / Search
//
    Route::group(['prefix' => 'search',], function () use ($perPage) {
        Route::get('/', function (Request $request) use ($perPage) {
            $query = $request->__get('query');
            return Reader::searchOriginal($query)
                ->paginate($perPage);
        });

        Route::get('/translation', function (Request $request) use ($perPage) {
            $query = $request->__get('query');
            return Reader::searchTranslation($query)
                ->paginate($perPage);
        });

        Route::get('/similarity/{para_id}', function ($paraID) use ($perPage) {
            return SearchSimilar::similarParagraph($paraID)
                ->paginate($perPage);
            // return SearchSimilar::similarParagraphStandard($paraID); // @todo: check which is better
        });

        Route::get('/cluster', function (Request $request) use ($perPage) {
            $query = $request->__get('query');

            $cover = $request->__get('cover');
            $covers = null == $cover ? $request->__get('covers') : $cover;
            $covered = null == $cover ? $request->__get('covered') : $cover;

            $reference = $request->__get('reference');

            return SearchSimilar::original($query, $covers, $covered, $reference)
                ->paginate($perPage);
        });

    });


//
// Reader / ZIP
//
    Route::group(['prefix' => 'zip',], function () {
        Route::get('/book/{code}', function ($code) {
            return response()->file(ZipJson::create($code, Reader::original($code)), ZipJson::header($code));
        });

        Route::get('/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}', function ($code, $lang = null, $publisher = null, $year = null, $no = null) {
            return response()->file(ZipJson::create($code, Reader::translations($code, $lang, $publisher, $year, $no)), ZipJson::header($code));
        });

        Route::get('/paragraph/{refcode_short}/{lang?}/{publisher?}/{year?}/{no?}', function ($refcodeShort, $lang = null, $publisher = null, $year = null, $no = null) {
            $refcodeShort = Reader::filterCode($refcodeShort);
            return response()->file(ZipJson::create($refcodeShort, Reader::paragraph($refcodeShort, $lang, $publisher, $year, $no)), ZipJson::header($refcodeShort));
        });
    });
});

//
// Sabbath School
//
Route::prefix('sabbathschool')
    ->group(function () use ($perPage) {
        Route::get('/', function (Request $request) {
            return config('egwk.api_help.entries.sabbathschool');
        });

        Route::get('/list/', function (Request $request) {
            return SabbathSchool::getList();
        });
        Route::get('/date/{date}/', function ($date) {
            return SabbathSchool::getByDate($date);
        });
        Route::get('/html/{date}/', function ($date) {
            return view('sabbathschool::html', array_merge(SabbathSchool::getByDate($date), ['title' => $date]));
        });
        Route::get('/{year}/{quarter?}/', function ($year, $quarter = 1) {
            return SabbathSchool::getQuarter($year, $quarter);
        });
        Route::get('/weeks/{year}/{quarter?}/', function ($year, $quarter = 1) {
            return SabbathSchool::getNoWeeks($year, $quarter);
        });
        Route::get('/week/{year}/{quarter}/{weekNo}', function ($year, $quarter, $weekNo) {
            /* not working */
            return SabbathSchool::getContentByWeekNo($year, $quarter, $weekNo);
        });
    });

//
// Hymnal
//
Route::middleware('auth:api')->group(function () use ($perPage) { // todo: test
    Route::group(['prefix' => 'hymnals',], function () use ($perPage) {
        Route::get('/languages', 'HymnalController@languages');
        Route::get('/{lang?}', 'HymnalController@hymnals');
    });
});

Route::group(['prefix' => 'hymnal',], function () use ($perPage) {
    Route::get('/{slug}', 'HymnalController@hymnalToc')->defaults('limit', $perPage);
    Route::get('/{slug}/{no}', 'HymnalController@hymnalEntry');
});

Route::group(['prefix' => 'hymn',], function () use ($perPage) {
    Route::get('/{slug}/{no}/{verse?}', 'HymnalController@hymnVerses');
    Route::get('/translate/{lang}/{slug}/{no}/{verses?}', 'HymnalController@translate');
    Route::get('/score/{slug}/{no}/{verses?}', 'HymnalController@score');
});

//
// Synch tool: Translation draft synchronization
//
Route::group(['prefix' => 'synch',], function () use ($perPage) {
    Route::get('/translations', 'SynchController@translations');
    Route::get('/{translationCode}', 'SynchController@synch')->defaults('limit', $perPage);
    Route::post('/{translationCode}', 'SynchController@save')->defaults('limit', $perPage);
});

//
// @todo further routes
//
Route::get('/devotionals', function () use ($perPage) {
    return DB::table('api_book')
        ->where('primary_collection_text_id', 'LIKE', 'devotionals')
        ->paginate($perPage);
});

