<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEditionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edition', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('book_code', 64);
			$table->string('tr_code', 64);
			$table->string('tr_title')->default('');
			$table->string('tr_title_alt')->default('');
			$table->string('publisher_code', 32)->default('unknown');
			$table->string('year', 32)->default('2016');
			$table->integer('no')->nullable()->default(1);
			$table->integer('version')->default(1);
			$table->string('start_para_id', 64);
			$table->string('section_element_type', 16)->nullable()->default('h2');
			$table->string('chapter_element_type', 16)->nullable()->default('h3');
			$table->string('translator')->nullable();
			$table->string('language', 3);
			$table->integer('user_level')->default(99);
			$table->string('source');
			$table->date('added')->nullable();
			$table->text('summary', 65535)->nullable();
			$table->boolean('church_approved')->nullable()->default(0);
			$table->boolean('status')->nullable()->default(0)->comment('0-done, 1-under_translation, 2-under_proof_reading, 3-needs_proof_reading');
			$table->string('text_id');
			$table->string('text_id_alt')->default('');
			$table->boolean('visible')->default(1);
			$table->unique(['tr_code','publisher_code','year'], 'tr_code');
		});

        DB::table('edition')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edition');
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
