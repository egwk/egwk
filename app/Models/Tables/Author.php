<?php

namespace App\Models\Tables;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Author
 */
class Author extends Model
{

    protected $table = 'author';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'life',
        'biography',
        'notes',
        'primary_language_id'
    ];
    protected $guarded = [];

}
