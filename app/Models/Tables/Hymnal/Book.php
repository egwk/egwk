<?php

namespace App\Models\Tables\Hymnal;

use Illuminate\Database\Eloquent\Model;

/**
 * Hynm Book
 */
class Book extends Model
{
    protected $table = 'hymnal_book';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'publisher',
        'year',
        'lang',
        'slug',
        'description',
        'permissions',
    ];

}
