<?php

namespace App\Models\Tables;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Original
 */
class SimilarParagraph extends Model
{

    use Searchable;

    protected $table = 'similarity_paragraph';
    public $timestamps = false;
    protected $fillable = [
        'para_id1',
        'w1',
        'para_id2',
        'w2',
    ];
    protected $guarded = ['id'];

    public function similars1()
    {
        return $this->hasMany('App\Models\Tables\Original', 'para_id2', 'para_id');
    }

    public function similars2()
    {
        return $this->hasMany('App\Models\Tables\Original', 'para_id1', 'para_id');
    }

    public function searchableAs()
    {
        return config('scout.prefix', 'e3si_') . 'similarity';
//        return config('scout.prefix', 'e3si_') . 'similarity1, ' . config('scout.prefix', 'e3si_') . 'similarity2 ';
    }

}
