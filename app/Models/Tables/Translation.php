<?php

namespace App\Models\Tables;

use Illuminate\Database\Eloquent\Model;
use \Laravel\Scout\Searchable;

/**
 * Class Translation
 */
class Translation extends Model
{

    use Searchable;

    protected $table = 'translation';
    public $timestamps = false;
    protected $fillable = [
        'para_id',
        'book_code',
        'lang',
        'publisher',
        'year',
        'no',
        'content'
    ];
    protected $guarded = [];

    public function original()
    {
        return $this->belongsTo(Original::class, 'para_id', 'para_id');
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher', 'code');
    }

    public function getContentAttribute($value)
    {
        return str_replace('¤n¤', '<br/>', $value);
    }

}
