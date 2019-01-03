<?php

namespace App\Models\Tables;

use Illuminate\Database\Eloquent\Model;

/**
 * News Author
 */
class News extends Model
{
    protected $table = 'news';

    public $timestamps = false;

    protected $fillable = [
        'ndate',
        'title',
        'text',
        'lead',
        'user_level',
        'important'
    ];

}
