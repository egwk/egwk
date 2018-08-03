<?php

namespace App\Models\Tables;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Original
 */
class Original extends Model
{

    use Searchable;

    protected $table = 'original';
    public $timestamps = false;
    protected $hidden = array('stemmed_wordlist');
    protected $fillable = [
        'id',
        'para_id',
        'id_prev',
        'id_next',
        'refcode_1',
        'refcode_2',
        'refcode_3',
        'refcode_4',
        'refcode_short',
        'refcode_long',
        'element_type',
        'element_subtype',
        'content',
        'puborder',
        'parent_1',
        'parent_2',
        'parent_3',
        'parent_4',
        'parent_5',
        'parent_6',
//        'stemmed_wordlist'
    ];
    protected $guarded = [];

    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }

    public function chapter()
    {
        return $this->belongsTo('App\Models\Tables\Original', 'parent_3', 'para_id');
    }

    public function section()
    {
        return $this->belongsTo('App\Models\Tables\Original', 'parent_2', 'para_id');
    }

    public function book()
    {
        return $this->belongsTo('App\Models\Tables\Original', 'parent_1', 'para_id');
    }

    public function book_chapter()
    {
        return $this->belongsTo('App\Models\Tables\Original', 'parent_1', 'para_id')
                        ->where('element_type', 'IN', ['h3', 'h2']);
    }

    public function publication()
    {
        return $this->belongsTo('App\Models\Tables\Publication', 'refcode_1', 'book_code');
//		return $this->belongsToMany('App\Models\Tables\Publication', 'publication_collection', 'collection_text_id', 'book_code');
    }

    public function paragraphs()
    {
        return $this->hasMany('App\Models\Tables\Original', 'parent_3', 'para_id');
    }

    public function chapters()
    {
        return $this
                        ->hasMany('App\Models\Tables\Original', 'parent_2', 'para_id')
                        ->where('element_type', 'h3');
    }

    public function book_chapters()
    {
        return $this->hasMany('App\Models\Tables\Original', 'parent_1', 'para_id')
                        ->whereIn('element_type', ['h2', 'h3']);
    }

    public function sections()
    {
        return $this->hasMany('App\Models\Tables\Original', 'parent_1', 'para_id')
                        ->where('element_type', 'h2');
    }

    public function translations()
    {
        return $this->hasMany('App\Models\Tables\Translation', 'para_id', 'para_id');
    }

}
