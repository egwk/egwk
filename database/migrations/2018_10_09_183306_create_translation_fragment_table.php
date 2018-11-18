<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTranslationFragmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('translation_fragment', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('para_id', 64)->nullable();
			$table->string('reference_text')->nullable();
			$table->text('content', 65535);
			$table->text('para_id_list')->nullable();
			$table->boolean('tr_type')->default(1)->comment('1-complete; 2-fragment; 3-multi');
		});

        DB::table('translation_fragment')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('translation_fragment');
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
