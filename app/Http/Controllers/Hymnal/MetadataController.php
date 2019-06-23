<?php

namespace App\Http\Controllers\Hymnal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tables\Hymnal\Book as HymnalBook;

class MetadataController extends Controller
{
    /**
     * Display the Hymnal metadata
     *
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        return \DB::table('api_hymnal_book')
            ->where('slug', $slug)
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $metadata = $request->toArray();
        try {
            $hymnal = HymnalBook::create($metadata);
            $hymnal->save();
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => 'Cannot save ' . array_get($metadata, 'slug', '-no-slug-')
            ];
        }
        return $hymnal;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        try {
            $hymnal = HymnalBook::where('slug', $slug)->first();
            if ($hymnal) {
                $hymnal
                    ->fill($request->toArray())
                    ->save();
            } else {
                return [
                    'error' => true,
                    'message' => 'Not found ' . $slug
                ];
            }
            return $hymnal;
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => 'Cannot save ' . $slug
            ];
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
