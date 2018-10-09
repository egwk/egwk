<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3TranslationWorkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('translation_work', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('para_id', 64)->index('para_id2');
			$table->string('book_code', 64);
			$table->string('lang', 2);
			$table->string('publisher', 32);
			$table->string('year', 32);
			$table->smallInteger('no');
			$table->text('content', 65535)->nullable()->index('content');
			$table->unique(['para_id','book_code','lang','publisher','year','no'], 'para_id');
		});

        DB::table('translation_work')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_translation_work');
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
