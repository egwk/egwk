<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3CacheSearchTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cache_search', function (Blueprint $table) {
            $table->integer('id')->default(0)->primary();
            $table->string('para_id', 64)->unique('para_id');
            $table->string('parent_1', 64)->nullable()->index('parent_1');
            $table->string('parent_2', 64)->nullable()->index('parent_2');
            $table->string('parent_3', 64)->nullable()->index('parent_3');
            $table->string('parent_4', 64)->nullable();
            $table->string('parent_5', 64)->nullable();
            $table->string('parent_6', 64)->nullable();
            $table->string('refcode_1', 32)->nullable();
            $table->string('refcode_2', 32)->nullable();
            $table->string('refcode_short');
            $table->text('refcode_long', 65535)->nullable();
            $table->text('book_title')->nullable();
            $table->text('section_title')->nullable();
            $table->text('chapter_title')->nullable();
            $table->text('content')->nullable();
            $table->text('stemmed_wordlist', 65535)->nullable();
            $table->string('element_subtype', 64)->nullable();
            $table->integer('puborder')->nullable();
            $table->string('year', 32)->nullable();
            $table->string('primary_collection_text_id', 128)->nullable();
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('egwk3_cache_search');
    }

}
