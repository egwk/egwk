<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectorSlidesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('projector_slides', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('channel_id');
			$table->integer('sequence');
			$table->text('content', 65535);
			$table->timestamps();
		});

        DB::table('projector_slides')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('projector_slides');
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
