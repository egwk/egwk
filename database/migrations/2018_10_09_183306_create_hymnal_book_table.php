<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHymnalBookTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hymnal_book', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('title', 128);
			$table->string('publisher', 128)->nullable();
			$table->string('year', 32)->nullable();
			$table->string('lang', 16)->nullable();
			$table->string('slug', 128)->nullable();
			$table->text('description', 65535)->nullable();
		});

        DB::table('hymnal_book')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('hymnal_book');
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
