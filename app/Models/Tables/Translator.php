<?php


namespace App\Models\Tables;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Translator
 */
class Translator extends Model
{
    protected $table = 'translator';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'from_language_id',
        'to_language_id',
        'church_approved'
    ];

    protected $guarded = [];

        
}