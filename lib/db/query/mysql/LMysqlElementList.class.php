<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlElementList {
	
	private $elements;

	private function checkNoArrayElementsOrElInside($arr) {
		foreach ($arr as $arr_element) {
			if (is_array($arr_element)) throw new \Exception("Invalid element list : contains an array");
			if ($arr_element instanceof self) throw new \Exception("Invalid element list : contains another element list");
		}
	}

	public function getElements() {
		return $this->elements;
	}

	public function __construct(... $elements) {

		if (count($elements)==0) throw new \Exception("Invalid element list : zero elements found - in mysql statement.");

		if (count($elements)==1 && is_array($elements[0])) {

			if (empty($elements[0])) throw new \Exception("Invalid element list : Empty array found - in mysql statement.");

			$this->checkNoArrayElementsOrElInside($elements[0]);

			$keys = array_keys($elements[0]);

			ensure_all_numbers("mysql element list",$keys);

			$this->elements = $elements[0];
		}
		else {

			$this->checkNoArrayElementsOrElInside($elements);

			$keys = array_keys($elements);

			ensure_all_numbers("mysql element list",$keys);

			$this->elements = $elements;
		}
	}

	private function asColumnNamesList() {
		$result = [];

		foreach ($this->elements as $el) $result[] = new LMysqlColumnName($el);

		return $result;
	}

	public function toRawStringList() {
		ensure_all_strings("elements in mysql element list",$this->elements);

		if (empty($this->elements)) return "";

		return "(".implode(',',$this->asColumnNamesList()).")";
	}

	public function toRawStringListWithoutParenthesis() {
		ensure_all_strings_or_null("elements in mysql element list",$this->elements);

		return implode(',',$this->asColumnNamesList());
	}

	public function toEscapedStringList() {
		ensure_all_numbers_or_strings_or_null("elements in mysql element list",$this->elements);

		$converted_elements = [];

		foreach ($this->elements as $elem) {
			$converted_elements[] = new LMysqlValueRenderer($elem);
		}

		return "(".implode(',',$converted_elements).")";
	}

	public function __toString() {
		return $this->toEscapedStringList();
	}

}