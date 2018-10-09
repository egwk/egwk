<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3SimilarityParagraphTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('similarity_paragraph', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('para_id1', 64);
			$table->float('w1');
			$table->string('para_id2', 64);
			$table->float('w2');
		});

        DB::table('similarity_paragraph')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_similarity_paragraph');
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
