<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEgwk3PublisherTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('publisher', function(Blueprint $table)
		{
			$table->string('code', 32)->primary();
			$table->string('name');
			$table->text('description', 65535)->nullable();
			$table->string('link')->nullable();
			$table->boolean('church_approved')->default(0);
		});

        DB::table('publisher')->insert($this->getData());

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('egwk3_publisher');
	}

	/**
	 * Get Data
	 *
	 * @return array
	 */
	private function getData()
	{
        return array(
		  array('code' => 'advent','name' => 'Advent Kiadó','description' => 'Advent Irodalmi Műhely','link' => 'http://adventkiado.hu/','church_approved' => '1'),
		  array('code' => 'bik','name' => 'BIK Kiadó','description' => 'Biblia Iskolák Közössége Kiadó','link' => 'http://bikkiado.hu/','church_approved' => '0'),
		  array('code' => 'felfedezesek','name' => 'Felfedezések Alapítvány','description' => NULL,'link' => 'http://azalapitvany.hu/','church_approved' => '1'),
		  array('code' => 'egervari-dezso','name' => 'Egervári Dezső','description' => 'A szerző saját kiadása','link' => NULL,'church_approved' => '0'),
		  array('code' => 'boldog-elet','name' => 'Boldog Élet Alapítvány','description' => NULL,'link' => NULL,'church_approved' => '0'),
		  array('code' => 'egwk','name' => 'Ellen Gould White Könyvtár','description' => 'Elektronikus kiadás az Ellen Gould White Könyvtár fordításában','link' => 'http://www.white-konyvtar.hu/','church_approved' => '0'),
		  array('code' => 'unpublished','name' => 'Unpublished','description' => 'Unpublished','link' => 'http://www.white-konyvtar.hu/','church_approved' => '0'),
		  array('code' => 'unknown','name' => 'Unknown','description' => 'Unknown','link' => 'http://www.white-konyvtar.hu/','church_approved' => '0'),
		  array('code' => 'gyarmati-es-tarsa','name' => 'Gyarmati és Társa','description' => 'Gyarmati és Társa: A Gyarmati és Bősz nyomda (1937-1948) kiadója, felelős: Bősz Ferenc
http://typographia.oszk.hu/html_clavis/hun/presslek.php?azon=4619','link' => 'http://typographia.oszk.hu/html_clavis/hun/presslek.php?azon=4619','church_approved' => '0'),
		  array('code' => 'igazsag-hirnoke','name' => 'Igazság Hírnöke Könyvterjesztő','description' => 'Igazság Hírnöke Könyvterjesztő','link' => NULL,'church_approved' => '0'),
		  array('code' => 'jelenvalo-igazsag-zurich','name' => 'Jelenvaló Igazság, Zürich','description' => 'Jelenvaló Igazság Kiadó, Zürich','link' => NULL,'church_approved' => '0'),
		  array('code' => 'advent, felfedezesek','name' => 'Advent Kiadó, Felfedezések Alapítvány','description' => 'Az Advent Irodalmi Műhely és a Felfedezések Alapítvány közös kiadása','link' => NULL,'church_approved' => '1'),
		  array('code' => 'elet-es-egeszseg','name' => 'Élet és Egészség Kiadó','description' => 'Élet és Egészség Kiadó','link' => NULL,'church_approved' => '1'),
		  array('code' => 'reform-ujvidek','name' => 'Hetednap Adventista Reformmozgalom, Újvidék','description' => 'Hetednap Adventista Reformmozgalom, Újvidék','link' => NULL,'church_approved' => '0'),
		  array('code' => 'bik, egwk','name' => 'BIK Kiadó, EGW Könyvtár','description' => 'A Biblia Iskolák Közössége Kiadó és az Ellen Gould White Könyvtár közös elektronikus kiadása','link' => NULL,'church_approved' => '0'),
		  array('code' => 'advent-ujvidek','name' => 'Keresztény Adventista Egyház, Újvidék','description' => 'Keresztény Adventista Egyház, Újvidék','link' => NULL,'church_approved' => '1'),
		  array('code' => 'viata-si-sanatate','name' => 'Viață și Sănătate','description' => 'Editura Viata si Sanatate SRL','link' => 'https://www.viatasisanatate.ro/','church_approved' => '1')
		);
    }
}
