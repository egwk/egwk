<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CacheSearchSeeder extends Seeder
{

    protected function dropTable($tableCacheSearch)
    {
        DB::statement("DROP TABLE IF EXISTS $tableCacheSearch;");
    }

    protected function createTable($tableCacheSearch, $tableOriginal, $tablePublication)
    {
        $query = <<<EOQ
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
EOQ;
        DB::statement($query);
    }

    protected function setYear($tableCacheSearch)
    {
        $query = <<<EOQ
			UPDATE $tableCacheSearch
				SET `year` = SUBSTR(refcode_1, -4)
				WHERE book_title LIKE 'Ms %' OR book_title LIKE 'Lt %';
EOQ;
        DB::statement($query);

        $query = <<<EOQ
			UPDATE `$tableCacheSearch`
				SET year = section_title
				WHERE section_title REGEXP '^[0-9]+$' AND `year` IN ('', NULL);
EOQ;
        DB::statement($query);
    }

    protected function setKeys($tableCacheSearch)
    {
        DB::statement("ALTER TABLE $tableCacheSearch ADD PRIMARY KEY (id) USING BTREE;");
        DB::statement("ALTER TABLE $tableCacheSearch ADD UNIQUE para_id (para_id) USING BTREE;");
    }

    protected function setIndices($tableCacheSearch)
    {
        DB::statement("ALTER TABLE $tableCacheSearch ADD INDEX parent_1 (parent_1) USING BTREE;");
        DB::statement("ALTER TABLE $tableCacheSearch ADD INDEX parent_2 (parent_2) USING BTREE;");
        DB::statement("ALTER TABLE $tableCacheSearch ADD INDEX parent_3 (parent_3) USING BTREE;");
    }

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

        Log::warning('Dropping table '. $tableCacheSearch);
        // $this->dropTable($tableCacheSearch);

        Log::warning('Creating table '. $tableCacheSearch);
        // $this->createTable($tableCacheSearch, $tableOriginal, $tablePublication);

        Log::info('Setting year for paragraphs where relevant');
        // $this->setYear($tableCacheSearch);

        Log::info('Setting up table keys');
        $this->setKeys($tableCacheSearch);

        Log::info('Setting up table indices');
        $this->setIndices($tableCacheSearch);

        Log::info('done.');
    }
}
