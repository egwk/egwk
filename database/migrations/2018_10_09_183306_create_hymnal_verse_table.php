<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHymnalVerseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hymnal_verse', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('hymnal_id');
			$table->string('hymn_no', 8);
			$table->string('verse_no', 12);
			$table->text('content', 65535)->nullable();
			$table->text('lily_hyphenated', 65535)->nullable();
			$table->text('note', 65535)->nullable();
		});

        DB::table('hymnal_verse')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('hymnal_verse');
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
