<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3OriginalWorkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('original_work', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('para_id', 64)->unique('para_id');
			$table->string('id_prev', 64)->nullable();
			$table->string('id_next', 64)->nullable();
			$table->string('refcode_1', 32)->nullable()->index('refcode_1');
			$table->string('refcode_2', 32)->nullable();
			$table->string('refcode_3', 32)->nullable();
			$table->string('refcode_4', 32)->nullable();
			$table->string('refcode_short');
			$table->text('refcode_long', 65535)->nullable();
			$table->string('element_type', 64)->nullable();
			$table->string('element_subtype', 64)->nullable();
			$table->text('content')->nullable();
			$table->integer('puborder')->nullable();
			$table->string('parent_1', 64)->nullable()->index('parent_1');
			$table->string('parent_2', 64)->nullable()->index('parent_2');
			$table->string('parent_3', 64)->nullable()->index('parent_3');
			$table->string('parent_4', 64)->nullable();
			$table->string('parent_5', 64)->nullable();
			$table->string('parent_6', 64)->nullable();
			$table->text('stemmed_wordlist', 65535)->nullable();
		});

        DB::table('original_work')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_original_work');
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
