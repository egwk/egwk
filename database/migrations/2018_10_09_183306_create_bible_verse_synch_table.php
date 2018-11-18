<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBibleVerseSynchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bible_verse_synch', function(Blueprint $table)
		{
			$table->integer('translation_id1');
			$table->integer('translation_id2');
			$table->integer('book_id');
			$table->integer('chapter1');
			$table->integer('verse1');
			$table->integer('chapter2');
			$table->integer('verse2');
		});

        DB::table('bible_verse_synch')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bible_verse_synch');
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
