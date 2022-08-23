<?php


class AlterTableColumnsTest extends LTestCase {
	

	function testDropTableColumns() {

		$db = db('framework_unit_tests');

		drop_table('big_table')->if_exists()->go($db);

		$l = table_list()->go($db);

		$this->assertFalse(array_value_exists('big_table',$l),"La tabella esiste prima di essere creata!");

		create_table('big_table')
			->column(col_def('id')->t_id())
			->column(col_def('data_inizio')->t_date()->not_null())
			->column(col_def('data_fine')->t_date())
			->column(col_def('cliente_id')->t_external_id()->not_null())
			->column(col_def('descrizione')->t_text())
			->column(col_def('conteggio_ore')->t_u_int()->not_null())
			->go($db);

		$l = table_list()->go($db);

		$this->assertTrue(array_value_exists('big_table',$l),"La tabella non è stata creata!");

		alter_table_columns('big_table')->drop_column('cliente_id')->drop_column('descrizione')->go($db);

		$td = table_description('big_table')->go($db);

		$this->assertTrue(array_key_exists('id',$td),"Il campo id nella tabella big_table non esiste!");
		$this->assertTrue(array_key_exists('data_inizio',$td),"Il campo id nella tabella big_table non esiste!");
		$this->assertFalse(array_key_exists('cliente_id',$td),"Il campo id nella tabella big_table non esiste!");
		$this->assertFalse(array_key_exists('descrizione',$td),"Il campo id nella tabella big_table non esiste!");
		
	}


}