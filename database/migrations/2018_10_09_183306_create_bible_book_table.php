<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBibleBookTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bible_book', function(Blueprint $table)
		{
			$table->boolean('translation_id')->default(0);
			$table->boolean('book_id')->default(0);
			$table->string('book', 50)->nullable();
			$table->string('book_sh', 15)->nullable();
			$table->boolean('chapter_number')->default(0);
			$table->primary(['translation_id','book_id']);
		});

		DB::table('bible_book')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bible_book');
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
