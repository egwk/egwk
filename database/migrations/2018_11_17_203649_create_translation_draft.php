<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationDraft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translation_draft', function (Blueprint $table) {
            $table->boolean('id')->primary();
            $table->string('code', 64);
            $table->integer('seq');
            $table->longText('content');
        });

        DB::table('translation_draft')->insert($this->getData());
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translation_draft');
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
