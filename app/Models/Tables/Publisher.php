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

	public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'link',
        'church_approved'
    ];

    protected $guarded = [];

	public function editions()
		{
		return $this->hasMany('App\Models\Tables\Edition', 'publisher_code', 'code');
		}
   
}