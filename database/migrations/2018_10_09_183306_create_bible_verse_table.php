<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBibleVerseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bible_verse', function(Blueprint $table)
		{
			$table->boolean('translation_id')->default(0);
			$table->boolean('book_id')->default(0);
			$table->boolean('chapter')->default(0);
			$table->boolean('verse')->default(0);
			$table->text('content', 65535);
			$table->integer('id', true);
		});

        DB::table('bible_verse')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bible_verse');
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
