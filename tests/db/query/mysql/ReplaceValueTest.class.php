<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class ReplaceValueTest extends LTestCase {
	

	function testReplaceValue() {

		db('framework_unit_tests');

		$r1 = __repl('column_name','@search_value','@replace_value');
		$r2 = __replace_value('column_name','@search_value','@replace_value');

		$this->assertEqual($r1,"REPLACE(column_name,'@search_value','@replace_value')","Il valore atteso dalla __repl non corrisponde a quello atteso!");
		$this->assertEqual($r2,"REPLACE(column_name,'@search_value','@replace_value')","Il valore atteso dalla __replace_value non corrisponde a quello atteso!");
		
	}

}