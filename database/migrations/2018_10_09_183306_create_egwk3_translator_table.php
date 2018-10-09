<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3TranslatorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('translator', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name');
			$table->string('from_language_id', 8);
			$table->string('to_language_id', 8);
			$table->boolean('church_approved')->default(0);
		});

        DB::table('translator')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_translator');
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
