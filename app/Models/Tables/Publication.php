<?php

namespace App\Models\Tables;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Publication
 */
class Publication extends Model
	{

	protected $table	 = 'publication';
	public $timestamps	 = false;
	public $incrementing = false;

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey	 = 'book_code';
	protected $fillable		 = [
		'book_code',
		'title',
		'html_title',
		'year',
		'author_id',
		'language_id',
		'primary_collection_text_id',
		'seq'
	];
	protected $guarded		 = [];

	public function paragraphs()
		{
		return $this->hasMany('App\Models\Tables\Original', 'refcode_1', 'book_code');
		}

	public function sections()
		{
		return $this->hasMany('App\Models\Tables\Original', 'refcode_1', 'book_code')
						->where('element_type', 'h2');
		}

	public function chapters()
		{
		return $this->hasMany('App\Models\Tables\Original', 'refcode_1', 'book_code')
						->where('element_type', 'h3');
		}

	public function collections()
		{
		return $this->belongsToMany('App\Models\Tables\Collection', 'publication_collection', 'book_code', 'collection_text_id');
		}

	public function editions()
		{
		return $this->hasMany('App\Models\Tables\Edition', 'book_code', 'book_code');
		}

	}
