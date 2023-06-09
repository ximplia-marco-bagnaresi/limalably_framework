<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class TarghettaAlberoManualDO extends LAbstractDataObject {
	

	const MY_TABLE = "targhetta_albero";

	public $id;
	public $codice_targhetta;

}

class ManualDataObjectTest extends LTestCase {
	

	function testBasicInsertSaveUpdateDelete() {

		$db = db('hosting_dreamhost_tests');

		truncate('targhetta_albero')->go($db);

		$t1 = new TarghettaAlberoManualDO();

		$t1->codice_targhetta = "abc1";

		$t1->saveOrUpdate($db);

		$this->assertEqual($t1->id,1,"L'id presente nel data object non corrisponde!");

		$t2 = new TarghettaAlberoManualDO();

		$t2->codice_targhetta = "abc2";

		$t2->saveOrUpdate($db);

		$this->assertEqual($t2->id,2,"L'id presente nel data object non corrisponde!");

		$result = select('count(*) as C','targhetta_albero')->go($db);

		$this->assertEqual($result[0]['C'],2,"Il numero di righe ritornate non corrisponde!");

		// caricamenti

		$t1_load_2 = new TarghettaAlberoManualDO(1,$db);

		$this->assertEqual($t1_load_2->codice_targhetta,'abc1',"Il codice della targhetta letto non corrisponde!!");

		$do = new TarghettaAlberoManualDO();

		$result = $do->findAll()->go();

		//echo $result;

		$this->assertEqual(count($result),2,"Il numero di elementi della classe non corrisponde!");

		$result = $do->findAll()->orderBy(desc('id'))->go();

		$this->assertEqual(count($result),2,"Il numero di elementi della classe non corrisponde!");

		$result = $do->findAll()->paginate(1,2)->go();

		$this->assertEqual(count($result),1,"Il numero di elementi della classe non corrisponde!");

		$first = $do->findFirst()->go();

		$this->assertTrue($first instanceof TarghettaAlberoManualDO,"L'oggetto non è della classe attesa!");

		$one = $do->findOne(_eq('id',1))->go();

		$this->assertTrue($first instanceof TarghettaAlberoManualDO,"L'oggetto non è della classe attesa!");
	}

}