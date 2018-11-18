<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePublicationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('publication', function(Blueprint $table)
		{
			$table->string('book_code', 64)->primary();
			$table->string('title');
			$table->string('html_title');
			$table->string('year', 32);
			$table->integer('author_id');
			$table->string('language_id', 3);
			$table->string('primary_collection_text_id', 128);
			$table->integer('seq')->default(999);
		});

        DB::table('publication')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('publication');
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
