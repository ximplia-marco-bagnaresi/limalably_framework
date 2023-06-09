<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LQueryFunctions {
		
	private static $initialized = false;
	private static $current_layer_name = null;

	public static function useMysqlLayer() {
		self::init();

		self::$current_layer_name = 'mysql';
	}

	public static function checkLayerSelected() {
		if (self::$current_layer_name==null) throw new \Exception("Query function layer is not selected correctly.");
	}

	public static function throwFunctionNotSupported($function_name) {
		throw new \Exception("In function query layer ".self::$current_layer_name." the function ".$function_name." is not supported!");
	}

	public static function usingMysqlLayer() {

		return self::$current_layer_name == 'mysql';
	}

	private static function throwQueryLayerNotFound() {
		throw new \Exception("Query function layer not found : ".self::$current_layer_name);
	}

	public static function init() {
		if (self::$initialized) return;

		self::$initialized = true;

		function last_affected_rows() {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlLastAffectedRows();

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function select($column_name_list,$table_name_list,$where_block=null) {
			 LQueryFunctions::checkLayerSelected();

			 if (LQueryFunctions::usingMysqlLayer()) return new LMysqlSelectStatement($column_name_list,$table_name_list,$where_block);

			 LQueryFunctions::throwQueryLayerNotFound();
		}

		function insert(string $table_name,$column_list=null,$data=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlInsertStatement($table_name,$column_list,$data);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function update(string $table_name,$name_value_pair_list,$where_block=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlUpdateStatement($table_name,$name_value_pair_list,$where_block);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function delete(string $table_name,$where_block=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlDeleteStatement($table_name,$where_block);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function replace(string $table_name,$column_list=null,$select_set_or_values=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlReplaceStatement($table_name,$column_list,$select_set_or_values);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function truncate(string $table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlTruncateTableStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _and(... $elements) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlAndBlock(... $elements);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _or(... $elements) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlOrBlock(... $elements);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _nl(string $column_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_null($column_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _is_null(string $column_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_null($column_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_nl(string $column_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_not_null($column_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}		

		function _is_not_null(string $column_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_not_null($column_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _eq(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::equal($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _eq_null(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::equal_null($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function _equal(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::equal($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _equal_null(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::equal_null($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_eq(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_equal($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_eq_null(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_equal_null($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function _not_equal(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_equal($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_equal_null(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_equal_null($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _gt(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function _greater_than(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _gt_eq(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than_or_equal($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _greater_than_or_equal(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than_or_equal($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _lt(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _less_than(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _lt_eq(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than_or_equal($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _less_than_or_equal(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than_or_equal($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _rlike(string $column_name,$pattern) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::rlike($column_name,$pattern);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _like(string $column_name,$pattern,$escape_char=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::like($column_name,$pattern,$escape_char);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_like(string $column_name,$pattern,$escape_char=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_like($column_name,$pattern,$escape_char);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _in(string $column_name,$data) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::in($column_name,$data);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_in(string $column_name,$data) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_in($column_name,$data);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _bt(string $column_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::between($column_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _between(string $column_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::between($column_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_bt(string $column_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_between($column_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_between(string $column_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_between($column_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _ex($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}		

		function _exists($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_ex($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_exists($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _match_against($table_list,$term_list,$boolean_mode=false) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::match_against($table_list,$term_list,$boolean_mode);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function _ifnull(string $column_name,$column_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlFunctions::ifnull($column_name,$column_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _replace_value(string $column_name,$search_value,$replace_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlReplaceValue($column_name,$search_value,$replace_value);
			
			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _repl(string $column_name,$search_value,$replace_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlReplaceValue($column_name,$search_value,$replace_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _case(string $column_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlCaseColumn($column_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _expr(string $text) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlExpression($text);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function wh($something) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlWhereBlock($something);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function where($something) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlWhereBlock($something);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function on($something) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlOnBlock($something);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function el(... $elements) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlElementList(... $elements);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function ell(... $lists) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlElementListList(... $lists);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function asc(string $column_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return c($column_name)." ASC";

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function desc(string $column_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return c($column_name)." DESC";

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function c(string $column_name,string $column_alias=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlColumnName($column_name,$column_alias);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function tn($table_def,string $table_alias = null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlTableName($table_def,$table_alias);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function query_list(string $query_list) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlQueryList($query_list);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function query_list_from_file($path_or_file) {
			LQueryFunctions::checkLayerSelected();

			if (is_string($path_or_file)) $par = new LFile($path_or_file);
			else $par = $path_or_file;

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlQueryList($par);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function table_list() {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlTableListStatement();

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function table_description(string $table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlTableDescriptionStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function create_table(string $table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlCreateTableStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function drop_table(string $table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlDropTableStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function rename_table(string $old_table_name,string $new_table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlRenameTableStatement($old_table_name,$new_table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function alter_table(string $table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlAlterTableStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function create_view(string $view_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlCreateViewStatement($view_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function drop_view(string $view_name) {	
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlDropViewStatement($view_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function view_list() {	
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlShowViewsStatement();

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function table_indexes_list(string $table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlDescribeIndexesStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function privileges_list() {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlShowPrivilegesStatement();

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function col_def(string $column_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlColumnDefinition($column_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function foreign_key_checks(bool $enable) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlForeignKeyChecksStatement($enable);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function fk_def(string $constraint_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlForeignKeyConstraintDefinition($constraint_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function csv_def($file_or_path) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlCsvDefinition($file_or_path);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function import_csv_into_table(string $table_name,$csv_def) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlImportCsvIntoTableStatement($file_or_path,$csv_def);

			LQueryFunctions::throwQueryLayerNotFound();
		}


	}



}