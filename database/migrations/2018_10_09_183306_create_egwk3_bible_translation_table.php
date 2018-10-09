<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3BibleTranslationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bible_translation', function(Blueprint $table)
		{
			$table->boolean('id')->primary();
			$table->string('code', 16)->default('')->unique('translation_key');
			$table->string('name');
			$table->string('short', 16);
			$table->string('after_chapter', 30)->nullable();
			$table->string('after_chapter_sh', 30)->nullable();
			$table->string('after_verse', 30)->nullable();
			$table->string('title_after_book', 15)->nullable();
			$table->string('title_after_chapter', 15)->nullable();
			$table->string('title_after_verse', 15)->nullable();
			$table->string('language', 3)->default('eng');
			$table->integer('user_level')->default(0);
		});

		DB::table('bible_translation')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_bible_translation');
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
