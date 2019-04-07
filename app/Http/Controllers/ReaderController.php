<?php

namespace App\Http\Controllers;

use App\Facades\Reader;
use App\Jobs\ComparePublications;
use App\Jobs\Exception\JobInProgressException;
use App\Jobs\Exception\QueueBusyException;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis as RedisFacade;
use App\Jobs\ComparePublications as ComparePublicationsJob;
use Facades\App\EGWK\Datamining\ComparePublications as ComparePublicationsClass;
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

    protected function compareCommon($books)
    {
        if (\Facades\App\Jobs\Job::areSameClassJobsInLimit(1, ComparePublications::class)) {
            throw new QueueBusyException;
        }

        $bookList = explode(',', $books);
        $path = ComparePublicationsClass::getFilePath($bookList);

        if (!\Storage::exists($path)) {
            ComparePublicationsJob::dispatch($bookList)
                ->onConnection('redis');
            throw new JobInProgressException;
        }

        return json_decode(\Storage::get($path), true);
    }

    public function compare($books)
    {
        try {
            $comparison = $this->compareCommon($books);
            $bookList = explode(',', $books);
            return collect($comparison)
                ->paginate($this->limit);
        } catch (QueueBusyException $e) {
            return ['error' => true, 'message' => 'QueueBusyException: Compiler is busy, please come back later.', 'books' => $bookList];
        } catch (JobInProgressException $e) {
            return ['error' => false, 'message' => 'JobInProgressException: Comparison process started.', 'books' => $bookList];
        }
    }

    /**
     * Comparison front-end action method
     * For testing only
     *
     * @param $books
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function compareTest($books)
    {
        $comparison = [];
        try {
            $comparison = $this->compareCommon($books);
        } catch (QueueBusyException $e) {
            return view('test.compare', ['data' => false, 'error' => true, 'message' => 'QueueBusyException: Compiler is busy, please come back later.', 'books' => $bookList = explode(',', $books)]);
        } catch (JobInProgressException $e) {
            // do nothing, just move forward
//            return view('test.compare', ['data' => false, 'error' => true, 'message' => 'JobInProgressException: Compiler is busy, please come back later.', 'books' => $bookList = explode(',', $books)]);
        }
        return view('test.compare', ['data' => $comparison, 'error' => false, 'books' => $bookList = explode(',', $books)]);
    }

}
