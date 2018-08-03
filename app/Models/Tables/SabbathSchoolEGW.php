<?php

namespace App\Models\Tables;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Edition
 */
class SabbathSchoolEGW extends Model
{

    protected $table = 'sabbathschool_egw';
    public $timestamps = false;
    protected $fillable = [
        'date',
        'seq',
        'content',
        'refcode_short',
    ];

}
