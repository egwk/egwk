<?php


namespace App\Models\Tables;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BookCollection
 */
class PublicationCollection extends Model
{
    protected $table = 'publication_collection';

    public $timestamps = false;

    protected $fillable = [
        'book_code',
        'collection_text_id'
    ];

    protected $guarded = [];

        
}