<?php

namespace App\Models\Tables;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Collection
 */
class Collection extends Model
    {

    protected $table      = 'collection';
    public $timestamps    = false;
    protected $primaryKey = 'text_id';
    public $incrementing  = false;
    protected $fillable   = [
        'seq'
    ];
    protected $guarded    = [];

    public function publications()
        {
        return $this->belongsToMany('App\Models\Tables\Publication', 'publication_collection', 'collection_text_id', 'book_code');
        }

//	public function editions()
//		{
//		return $this->hasManyThrough('App\Models\Tables\Edition', 'App\Models\Tables\Publication');
//		}
    }
