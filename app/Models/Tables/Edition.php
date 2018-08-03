<?php

namespace App\Models\Tables;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Edition
 */
class Edition extends Model
{

    protected $table = 'edition';
    public $timestamps = false;
    protected $fillable = [
        'book_code',
        'tr_code',
        'tr_title',
        'tr_title_alt',
        'publisher_code',
        'year',
        'no',
        'version',
        'start_para_id',
        'section_element_type',
        'chapter_element_type',
        'translator',
        'language',
        'user_level',
        'source',
        'added',
        'summary',
        'church_approved',
        'status',
        'text_id',
        'text_id_alt',
        'visible'
    ];
    protected $guarded = [
        'id'
    ];

    public function publication()
    {
        return $this->belongsTo('App\Models\Tables\Publication', 'book_code', 'book_code');
    }

    public function publisher()
    {
        return $this->belongsTo('App\Models\Tables\Publisher', 'publisher_code', 'code');
    }

}
