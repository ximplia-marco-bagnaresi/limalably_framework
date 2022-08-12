<?php


class LMysqlElementList {
	
	private $elements;

	public function __construct(... $elements) {
		$this->elements = $elements;
	}

	public function toRawStringList() {
		ensure_all_strings($this->elements)

		return "(".implode(',',$this->elements).")";
	}

	public function toRawStringListWithoutParenthesis() {
		ensure_all_strings($this->elements)

		return implode(',',$this->elements);
	}

	public function toEscapedStringList() {
		ensure_all_numbers_or_strings($this->elements);

		$converted_elements = [];

		foreach ($this->elements as $elem) {
			if (is_string($elem)) {
				$converted_elements[] = "'".$elem."'";
			} else {
				$converted_elements[] = $elem;
			}
		}

		return "(".implode(',',$converted_elements).")";
	}

	public function __toString() {
		return $this->toEscapedStringList();
	}

}