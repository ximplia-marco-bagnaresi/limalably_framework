<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlColumnDescription implements LIColumnDescription {
	
	private $column_name;
	private $column_type;
	private $is_null;
	private $key;
	private $default_value;
	private $extra_info;

	public function __construct($column_name,$column_type,$is_null,$key,$default_value,$extra_info) {
		$this->column_name = $column_name;
		$this->column_type = $column_type;
		$this->is_null = $is_null;
		$this->key = $key;
		$this->default_value = $default_value;
		$this->extra_info = $extra_info;
	}


	public function getColumnName() {
		return $this->column_name;
	}

	public function getColumnType() {
		return $this->column_type;
	}

	public function isNull() {
		return $this->is_null!='NO';
	}

	public function key() {
		return $this->key;
	}

	public function getDefaultValue() {
		if($this->default_value=='NULL') {
			return null;
		} else {
			return $this->default_value;
		}
	}

	public function getExtraInfo() {
		return $this->extra_info;
	}
}