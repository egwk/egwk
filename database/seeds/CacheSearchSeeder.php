<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CacheSearchSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
		$tableOriginal = env('DB_TABLE_PREFIX', 'db_') . 'original';
		$tablePublication = env('DB_TABLE_PREFIX', 'db_') . 'publication';
		$tableCacheSearch = env('DB_TABLE_PREFIX', 'db_') . 'cache_search';
	
        $query = <<<EOQ
DROP TABLE IF EXISTS $tableCacheSearch;

CREATE TABLE $tableCacheSearch AS 
    SELECT  o.id, o.para_id, o.parent_1, o.parent_2, o.parent_3, o.parent_4, o.parent_5, o.parent_6,
            o.refcode_1, o.refcode_2, o.refcode_short, o.refcode_long,
            h1.content AS book_title, h2.content AS section_title, h3.content AS chapter_title, o.content, o.stemmed_wordlist,
            o.element_subtype, o.puborder, p.year, p.primary_collection_text_id
    FROM $tableOriginal AS o
        LEFT JOIN $tableOriginal AS h1 ON (o.parent_1 = h1.para_id AND h1.element_type = 'h1')
        LEFT JOIN $tableOriginal AS h2 ON (o.parent_2 = h2.para_id AND h2.element_type = 'h2')
        LEFT JOIN $tableOriginal AS h3 ON (o.parent_3 = h3.para_id AND h3.element_type = 'h3')
        LEFT JOIN $tablePublication AS p ON (o.refcode_1 = p.book_code)
    WHERE o.element_type = 'p'
    ORDER BY p.seq, o.puborder;

UPDATE $tableCacheSearch
    SET `year` = SUBSTR(refcode_1, -4)
    WHERE book_title LIKE 'Ms %' OR book_title LIKE 'Lt %';

UPDATE `$tableCacheSearch`
    SET year = section_title
    WHERE section_title REGEXP '^[0-9]+$' AND `year` IN ('', NULL);

ALTER TABLE $tableCacheSearch ADD PRIMARY KEY (id) USING BTREE;
ALTER TABLE $tableCacheSearch ADD UNIQUE para_id (para_id) USING BTREE;
ALTER TABLE $tableCacheSearch ADD INDEX parent_1 (parent_1) USING BTREE;
ALTER TABLE $tableCacheSearch ADD INDEX parent_2 (parent_2) USING BTREE;
ALTER TABLE $tableCacheSearch ADD INDEX parent_3 (parent_3) USING BTREE;

EOQ;

		DB::statement($query);
    }
}
