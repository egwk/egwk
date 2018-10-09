<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3HymnalSynchTitleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hymnal_synch_title', function(Blueprint $table)
		{
			$table->boolean('book_title1');
			$table->boolean('lang1');
			$table->boolean('no1');
			$table->boolean('title1');
			$table->boolean('book_title2');
			$table->boolean('lang2');
			$table->boolean('no2');
			$table->boolean('title2');
		});

        DB::table('hymnal_synch_title')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_hymnal_synch_title');
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
