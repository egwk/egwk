<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiBookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tablePrefix = config('database.connections.' . config('database.default') . '.prefix');

        $createQuery = "CREATE TABLE `${tablePrefix}api_book` select `${tablePrefix}translation`.`book_code` AS `book_code`,`${tablePrefix}edition`.`tr_code` AS `tr_code`,substr(`${tablePrefix}translation`.`para_id`,1,(locate('.',`${tablePrefix}translation`.`para_id`) - 1)) AS `book_id`,`${tablePrefix}original`.`content` AS `title`,`${tablePrefix}translation`.`content` AS `tr_title`,`${tablePrefix}edition`.`tr_title_alt` AS `tr_title_alt`,`${tablePrefix}edition`.`summary` AS `summary`,`${tablePrefix}edition`.`translator` AS `translator`,`${tablePrefix}translation`.`lang` AS `lang`,`${tablePrefix}translation`.`publisher` AS `publisher`,`${tablePrefix}translation`.`year` AS `year`,`${tablePrefix}translation`.`no` AS `no`,`${tablePrefix}publisher`.`name` AS `publisher_name`,`${tablePrefix}publication`.`primary_collection_text_id` AS `primary_collection_text_id`,`${tablePrefix}publication`.`seq` AS `seq`,`${tablePrefix}edition`.`text_id` AS `text_id`,`${tablePrefix}edition`.`text_id_alt` AS `text_id_alt`,`${tablePrefix}edition`.`church_approved` AS `church_approved`,concat_ws('/',`${tablePrefix}translation`.`book_code`,`${tablePrefix}translation`.`lang`,`${tablePrefix}translation`.`publisher`,`${tablePrefix}translation`.`year`,`${tablePrefix}translation`.`no`) AS `edition_id`,concat_ws('/','','book',`${tablePrefix}translation`.`book_code`) AS `book_uri`,concat_ws('/','','toc',`${tablePrefix}translation`.`book_code`,`${tablePrefix}translation`.`lang`,`${tablePrefix}translation`.`publisher`,`${tablePrefix}translation`.`year`,`${tablePrefix}translation`.`no`) AS `toc_uri`,concat_ws('/','','translation',`${tablePrefix}translation`.`book_code`,`${tablePrefix}translation`.`lang`,`${tablePrefix}translation`.`publisher`,`${tablePrefix}translation`.`year`,`${tablePrefix}translation`.`no`) AS `translation_uri`,concat_ws('/','','zip','translation',`${tablePrefix}translation`.`book_code`,`${tablePrefix}translation`.`lang`,`${tablePrefix}translation`.`publisher`,`${tablePrefix}translation`.`year`,`${tablePrefix}translation`.`no`) AS `zip_uri` from ((((`${tablePrefix}translation` join `${tablePrefix}edition` on(((`${tablePrefix}translation`.`book_code` = `${tablePrefix}edition`.`book_code`) and (`${tablePrefix}translation`.`publisher` = `${tablePrefix}edition`.`publisher_code`) and (`${tablePrefix}translation`.`year` = `${tablePrefix}edition`.`year`) and (`${tablePrefix}translation`.`no` = `${tablePrefix}edition`.`no`)))) join `${tablePrefix}publisher` on((`${tablePrefix}translation`.`publisher` = `${tablePrefix}publisher`.`code`))) join `${tablePrefix}publication` on((`${tablePrefix}translation`.`book_code` = `${tablePrefix}publication`.`book_code`))) JOIN `${tablePrefix}original` on((`${tablePrefix}original`.`para_id` = `${tablePrefix}translation`.`para_id`))) WHERE (`${tablePrefix}original`.`puborder` = 1) ORDER BY `${tablePrefix}publication`.`seq`,`${tablePrefix}edition`.`church_approved` desc;";

        \DB::statement($createQuery);

        $alterQuery = "ALTER TABLE `${tablePrefix}api_book` ADD PRIMARY KEY (`book_code`,`lang`,`publisher`,`year`,`no`);";

        \DB::statement($alterQuery);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_book');
    }
}
