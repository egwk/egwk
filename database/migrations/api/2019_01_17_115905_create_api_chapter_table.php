<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiChapterTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tablePrefix = config('database.connections.' . config('database.default') . '.prefix');

        $createQuery = "CREATE TABLE `${tablePrefix}api_chapter`  AS  select `${tablePrefix}original`.`para_id` AS `para_id`,`${tablePrefix}original`.`id_prev` AS `id_prev`,`${tablePrefix}original`.`id_next` AS `id_next`,`${tablePrefix}original`.`refcode_1` AS `refcode_1`,`${tablePrefix}original`.`refcode_2` AS `refcode_2`,`${tablePrefix}original`.`refcode_3` AS `refcode_3`,`${tablePrefix}original`.`refcode_4` AS `refcode_4`,`${tablePrefix}original`.`refcode_short` AS `refcode_short`,`${tablePrefix}original`.`refcode_long` AS `refcode_long`,`${tablePrefix}original`.`element_type` AS `element_type`,`${tablePrefix}original`.`element_subtype` AS `element_subtype`,`${tablePrefix}original`.`content` AS `content`,`${tablePrefix}original`.`puborder` AS `puborder`,`${tablePrefix}original`.`parent_1` AS `parent_1`,`${tablePrefix}original`.`parent_2` AS `parent_2`,`${tablePrefix}original`.`parent_3` AS `parent_3`,`${tablePrefix}original`.`parent_4` AS `parent_4`,`${tablePrefix}original`.`parent_5` AS `parent_5`,`${tablePrefix}original`.`parent_6` AS `parent_6`,`${tablePrefix}translation`.`lang` AS `lang`,`${tablePrefix}translation`.`publisher` AS `publisher`,`${tablePrefix}translation`.`year` AS `year`,`${tablePrefix}translation`.`no` AS `no`,`${tablePrefix}translation`.`content` AS `tr_content`,concat_ws('/','','toc',`${tablePrefix}original`.`refcode_1`,convert(`${tablePrefix}translation`.`lang` using utf8mb4),convert(`${tablePrefix}translation`.`publisher` using utf8mb4),convert(`${tablePrefix}translation`.`year` using utf8mb4),`${tablePrefix}translation`.`no`) AS `toc_uri` FROM (`${tablePrefix}original` join `${tablePrefix}translation` on((`${tablePrefix}original`.`para_id` = `${tablePrefix}translation`.`para_id`))) WHERE (`${tablePrefix}original`.`element_type` not in ('h1','h2','h3')) ORDER BY `${tablePrefix}original`.`puborder`;";

        \DB::statement($createQuery);

        $alterQuery = "ALTER TABLE `${tablePrefix}api_chapter` ADD PRIMARY KEY (`para_id`,`lang`,`publisher`,`year`,`no`), ADD KEY `parent_1` (`parent_1`), ADD KEY `parent_2` (`parent_2`), ADD KEY `parent_3` (`parent_3`), ADD KEY `refcode_short` (`refcode_short`);";

        \DB::statement($alterQuery);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_chapter');
    }
}
