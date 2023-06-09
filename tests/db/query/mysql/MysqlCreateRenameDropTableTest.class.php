<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class MysqlCreateRenameDropTableTest extends LTestCase {
	

	function testShowCreateTable() {

		$db = db('hosting_dreamhost_tests');

		$result = create_table('regione')->show()->go($db);

		$this->assertTrue(strlen($result)>10,"La create table non è stata ritornata correttamente!");	
		
	}

	function testSafeCreateUpdateDeleteColumns() {

		$db = db('hosting_dreamhost_tests');

		drop_table('my_test_table2')->if_exists()->go($db);

		create_table('my_test_table2')->column(col_def('my_column')->t_boolean())->standard_operations_columns()->go($db);

		$table_list = table_list()->go($db);

		$this->assertTrue(array_value_exists('my_test_table2',$table_list),"La tabella 'my_test_table2' non è stata creata!");
	}

	function testCreateRenameDropTable() {

		$db = db('hosting_dreamhost_tests');

		drop_table('my_test_table')->if_exists()->go($db);

		drop_table('my_another_table')->if_exists()->go($db);

		create_table('my_test_table')->column(col_def('my_column')->t_boolean())->go($db);

		$table_list = table_list()->go($db);

		$this->assertTrue(array_value_exists('my_test_table',$table_list),"La tabella 'my_test_table' non è stata creata!");

		rename_table('my_test_table','my_another_table')->go($db);

		$table_list = table_list()->go($db);

		$this->assertTrue(array_value_exists('my_another_table',$table_list),"La tabella 'my_another_table' non è stata trovata!");

		$this->assertFalse(array_value_exists('my_test_table',$table_list),"La tabella 'my_test_table' esiste ancora!");
		
		drop_table('my_another_table')->go($db);

		$table_list = table_list()->go($db);

		$this->assertFalse(array_value_exists('my_test_table',$table_list),"La tabella 'my_test_table' esiste ancora!");
		$this->assertFalse(array_value_exists('my_another_table',$table_list),"La tabella 'my_another_table' esiste ancora!");

	}

	function testCreateDropBigTable() {

		$db = db('hosting_dreamhost_tests');

		drop_table('big_table')->if_exists()->go($db);

		$l = table_list()->go($db);

		$this->assertFalse(array_value_exists('big_table',$l),"La tabella esiste prima di essere creata!");

		create_table('big_table')
			->column(col_def('id')->t_id())
			->column(col_def('data_inizio')->t_date()->not_null())
			->column(col_def('data_fine')->t_date())
			->column(col_def('cliente_id')->t_external_id()->not_null())
			->column(col_def('descrizione')->t_text())
			->column(col_def('conteggio_ore')->t_u_int()->not_null())->charset('utf8mb4')
			->go($db);

		$l = table_list()->go($db);

		$this->assertTrue(array_value_exists('big_table',$l),"La tabella non è stata creata!");

		drop_table('big_table')->go($db);

		$l = table_list()->go($db);

		$this->assertFalse(array_value_exists('big_table',$l),"La tabella non è stata eliminata!");


	}


}