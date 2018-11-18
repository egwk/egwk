<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePublicationCollectionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('publication_collection', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('book_code', 64);
			$table->string('collection_text_id', 128);
		});

        DB::table('publication_collection')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('publication_collection');
	}

	/**
	 * Get Data
	 *
	 * @return array
	 */
	private function getData()
	{
        return [
        ];
    }
}
