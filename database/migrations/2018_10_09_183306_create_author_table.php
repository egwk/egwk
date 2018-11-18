<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('author', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name');
			$table->string('life', 32);
			$table->text('biography', 65535)->nullable();
			$table->text('notes', 65535)->nullable();
			$table->string('primary_language_id', 8);
		});

		DB::table('author')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('author');
	}

	/**
	 * Get Data
	 *
	 * @return array
	 */
	private function getData()
	{
        return array(
		  array('id' => '1','name' => 'Ellen Gould White','life' => '1827-1915','biography' => NULL,'notes' => NULL,'primary_language_id' => 'en')
		);
    }
}
