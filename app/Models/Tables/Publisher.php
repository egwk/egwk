<?php


namespace App\Models\Tables;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Publisher
 */
class Publisher extends Model
{
    protected $table = 'publisher';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'link',
        'church_approved',
        'priority'
    ];

    protected $guarded = [];

    public function editions()
    {
        return $this->hasMany('App\Models\Tables\Edition', 'publisher_code', 'code');
    }

}
