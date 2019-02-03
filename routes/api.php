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

Route::get('/', 'Controller@help');

Route::middleware('auth:api')->group(function () {
}); // todo: test

Route::fallback('HomeController@notFound')->name('api.fallback.404');

/*
|--------------------------------------------------------------------------
| Routes for EGWK modules
|--------------------------------------------------------------------------
*/

//
// Reader
//
Route::group(['prefix' => 'reader',], function () {

    Route::get('/', 'ReaderController@help')->defaults('module', 'reader');
    Route::get('/books/{lang?}', 'ReaderController@books');
    Route::get('/collections/{lang?}', 'ReaderController@collections');
    Route::get('/book/{code}', 'ReaderController@book');
    Route::get('/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}', 'ReaderController@toc');
    Route::get('/chapter/{code}/{lang?}/{publisher?}/{year?}/{no?}', 'ReaderController@chapter');
    Route::get('/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}', 'ReaderController@translation');
    Route::get('/parallel/{code}/{lang?}/{publisher?}/{year?}/{no?}', 'ReaderController@parallel');
    Route::get('/paragraph/{refcode_short}/{lang?}/{publisher?}/{year?}/{no?}', 'ReaderController@paragraph');

//
// Reader / Metadata
//
    Route::group(['prefix' => 'metadata',], function () {
        Route::get('/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}', 'Reader\\MetadataController@toc');
        Route::get('/chapter/{code}/{lang?}/{publisher?}/{year?}/{no?}', 'Reader\\MetadataController@chapter');
    });

//
// Reader / Search
//
    Route::group(['prefix' => 'search',], function () {
        Route::get('/', 'Reader\\SearchController@search');
        Route::get('/translation', 'Reader\\SearchController@translation');
        Route::get('/similarity/{para_id}', 'Reader\\SearchController@similarity');
        Route::get('/cluster/{lang?}', 'Reader\\SearchController@cluster');
    });

//
// Reader / ZIP
//
    Route::group(['prefix' => 'zip',], function () {
        Route::get('/book/{code}', 'Reader\\ZipController@book');
        Route::get('/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}', 'Reader\\ZipController@translation');
        Route::get('/paragraph/{refcode_short}/{lang?}/{publisher?}/{year?}/{no?}', 'Reader\\ZipController@paragraph');
    });
});

//
// Sabbath School
//
Route::prefix('sabbathschool')
    ->group(function () {
        Route::get('/', 'SabbathSchoolController@help')->defaults('module', 'sabbathschool');
        Route::get('/list/', 'SabbathSchoolController@list');
        Route::get('/date/{date}/', 'SabbathSchoolController@date');
        Route::get('/html/{date}/', 'SabbathSchoolController@html');
        Route::get('/{year}/{quarter?}/', 'SabbathSchoolController@quarter');
        Route::get('/weeks/{year}/{quarter?}/', 'SabbathSchoolController@weeks');
        Route::get('/week/{year}/{quarter}/{weekNo}', 'SabbathSchoolController@week');
    });

//
// Hymnal
//
Route::group(['prefix' => 'hymnals',], function () {
    Route::get('/languages', 'HymnalController@languages');
    Route::get('/{lang?}', 'HymnalController@hymnals');
});

Route::group(['prefix' => 'hymnal',], function () {
    Route::get('/{slug}/metadata', 'HymnalController@hymnalMetadata');
    Route::get('/{slug}', 'HymnalController@hymnalToc');
    Route::get('/{slug}/{no}', 'HymnalController@hymnalEntry');
});

Route::group(['prefix' => 'hymn',], function () {
    Route::get('/{slug}/{no}/{verse?}', 'HymnalController@hymnVerses');
    Route::get('/translate/{lang}/{slug}/{no}/{verses?}', 'HymnalController@translate');
    Route::get('/score/{slug}/{no}/{verses?}', 'HymnalController@score');
});

//
// Synch tool: Translation draft synchronization
//
Route::group(['prefix' => 'synch',], function () {
    Route::get('/translations', 'SynchController@translations');
    Route::get('/{translationCode}', 'SynchController@synch');
    Route::post('/{translationCode}', 'SynchController@save');
});

//
// News
//
Route::group(['prefix' => 'news',], function () {
    Route::get('/', 'NewsController@all');
    Route::get('/news', 'NewsController@news');
    Route::get('/pinned', 'NewsController@pinned');
    Route::get('/others', 'NewsController@others');
    Route::get('/books', 'NewsController@books');
});

//
// @todo further routes
//
/*
Route::get('/devotionals', function () {
    $perPage = 25;
    return DB::table('api_book')
        ->where('primary_collection_text_id', 'LIKE', 'devotionals')
        ->paginate($perPage);
});
*/
