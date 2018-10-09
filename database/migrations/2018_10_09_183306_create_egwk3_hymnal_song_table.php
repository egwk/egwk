<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3HymnalSongTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hymnal_song', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('hymnal_id');
			$table->string('hymn_no', 8);
			$table->string('title', 128)->index('title_2');
			$table->string('composer')->nullable();
			$table->string('poet')->nullable();
			$table->string('translation')->nullable();
			$table->string('arranger')->nullable();
			$table->string('tune')->nullable();
			$table->string('tune_year', 32)->nullable();
			$table->string('lyrics_year', 32)->nullable();
			$table->string('scripture', 128)->nullable();
			$table->string('topic', 128)->nullable();
			$table->text('info', 65535)->nullable();
			$table->string('copyright')->nullable();
			$table->text('lily_score')->nullable();
		});

        DB::table('hymnal_song')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_hymnal_song');
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
