<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCollectionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('collection', function(Blueprint $table)
		{
			$table->string('text_id', 128)->primary();
			$table->integer('seq');
		});

        DB::table('collection')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('collection');
	}

	/**
	 * Get Data
	 *
	 * @return array
	 */
	private function getData()
	{
        return array(
		  array('text_id' => 'biography','seq' => '40'),
		  array('text_id' => 'conflict-of-the-ages','seq' => '1'),
		  array('text_id' => 'devotionals','seq' => '3'),
		  array('text_id' => 'early-writings','seq' => '7'),
		  array('text_id' => 'education','seq' => '5'),
		  array('text_id' => 'exegesis','seq' => '10'),
		  array('text_id' => 'health','seq' => '4'),
		  array('text_id' => 'letters-and-manuscripts','seq' => '900'),
		  array('text_id' => 'manuscript-releases','seq' => '51'),
		  array('text_id' => 'miscellaneous','seq' => '99'),
		  array('text_id' => 'mission','seq' => '6'),
		  array('text_id' => 'modern-english','seq' => '200'),
		  array('text_id' => 'periodicals','seq' => '50'),
		  array('text_id' => 'prophecies','seq' => '8'),
		  array('text_id' => 'revival-and-reformation','seq' => '11'),
		  array('text_id' => 'teachings-of-christ','seq' => '9'),
		  array('text_id' => 'testimonies','seq' => '2')
		);
    }
}
