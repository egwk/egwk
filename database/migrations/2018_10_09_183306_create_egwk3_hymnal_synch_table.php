<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3HymnalSynchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hymnal_synch', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('hymnal1_id');
			$table->string('hymn1_no', 8);
			$table->integer('hymnal2_id');
			$table->string('hymn2_no', 8);
			$table->string('type', 32)->nullable()->default('tune');
			$table->text('note', 65535)->nullable();
		});

        DB::table('hymnal_synch')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_hymnal_synch');
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
