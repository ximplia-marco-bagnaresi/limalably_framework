<?php

class TarghettaAlberoAutoDO extends LAbstractDataObject {
	

	const TABLE = "targhetta_albero";

}

class AutoDataObjectTest extends LTestCase {
	

	function testBasicInsertSaveUpdateDelete() {
		
		$db = db('framework_unit_tests');

		truncate('targhetta_albero')->go($db);

		$t1 = new TarghettaAlberoAutoDO();

		$t1->codice_targhetta = "abc1";

		$t1->saveOrUpdate($db);

		$this->assertEqual($t1->id,1,"L'id presente nel data object non corrisponde!");

		$t2 = new TarghettaAlberoAutoDO();

		$t2->codice_targhetta = "abc2";

		$t2->saveOrUpdate($db);

		$this->assertEqual($t2->id,2,"L'id presente nel data object non corrisponde!");

		$result = select('count(*) as C','targhetta_albero')->go($db);

		$this->assertEqual($result[0]['C'],2,"Il numero di righe ritornate non corrisponde!");

		// caricamenti

		$t1_load_2 = new TarghettaAlberoAutoDO(1,$db);

		$this->assertEqual($t1_load_2->codice_targhetta,'abc1',"Il codice della targhetta letto non corrisponde!!");

		$result = TarghettaAlberoAutoDO::findAll()::go();

		echo $result;

		$this->assertEqual(count($result),2,"Il numero di elementi della classe non corrisponde!");

		$first = TarghettaAlberoAutoDO::findFirst()::go();

		$this->assertTrue($first instanceof TarghettaAlberoAutoDO,"L'oggetto non è della classe attesa!");

		$one = TarghettaAlberoAutoDO::findOne(_eq('id',1))::go();

		$this->assertTrue($first instanceof TarghettaAlberoAutoDO,"L'oggetto non è della classe attesa!");

		$count = TarghettaAlberoAutoDO::count()::go();

		$this->assertEqual($count,2,"Il numero di righe trovate nella tabella non corrisponde a quelle attese!");

	}

	
}