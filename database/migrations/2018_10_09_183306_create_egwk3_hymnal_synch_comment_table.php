<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3HymnalSynchCommentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hymnal_synch_comment', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('hymnal_id');
			$table->string('hymn_no', 8);
			$table->text('comment', 65535)->nullable();
		});

        DB::table('hymnal_synch_comment')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_hymnal_synch_comment');
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
