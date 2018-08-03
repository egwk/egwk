<?php

namespace App\Models\Tables;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CacheSearch
 */
class CacheSearch extends Model
    {

    use Searchable;

    public $timestamps = false;
    protected $table = 'cache_search';
    protected $fillable = [
        'id',
        'para_id',
        'parent_1',
        'parent_2',
        'parent_3',
        'parent_4',
        'parent_5',
        'parent_6',
        'refcode_1',
        'refcode_2',
        'refcode_short',
        'refcode_long',
        'book_title',
        'section_title',
        'chapter_title',
        'content',
        'stemmed_wordlist',
        'element_subtype',
        'puborder',
        'year',
        'primary_collection_text_id',
    ];

    protected $guarded = [];

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
        {
        return env('SCOUT_PREFIX', 'e3si_') . 'search';
        }

    }
