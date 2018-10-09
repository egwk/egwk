<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3LanguageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language', function(Blueprint $table)
		{
			$table->string('id', 3)->primary();
			$table->string('name', 64);
		});

        DB::table('language')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_language');
	}

	/**
	 * Get Data
	 *
	 * @return array
	 */
	private function getData()
	{
        return array(
		  array('id' => 'en','name' => 'English'),
		  array('id' => 'hu','name' => 'Magyar')
		);
    }
}
