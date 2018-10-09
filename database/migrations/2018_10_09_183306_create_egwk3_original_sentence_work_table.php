<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3OriginalSentenceWorkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('original_sentence_work', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('para_id', 64);
			$table->integer('index')->nullable();
			$table->text('content')->nullable();
			$table->text('stemmed_wordlist', 65535)->nullable();
			$table->unique(['para_id','index'], 'para_id');
		});

        DB::table('original_sentence_work')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_original_sentence_work');
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
