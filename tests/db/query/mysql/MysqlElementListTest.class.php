<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MysqlElementListTest extends LTestCase {
	
	function testElementListWithString() {

		db('hosting_dreamhost_tests');

		$el = el('prova');

		$this->assertEqual($el->toRawStringList(),"(prova)","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toRawStringListWithoutParenthesis(),"prova","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toEscapedStringList(),"('prova')","La stringa della lista di elementi non corrisponde a quello atteso!");
	}

	function testElementListWithMultipleParametersWithAdd() {

		db('hosting_dreamhost_tests');

		$el = el();

		$el->add('elemento1');
		$el->add('elemento2');
		$el->add('elemento3');			

		$this->assertEqual($el->toRawStringList(),"(elemento1,elemento2,elemento3)","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toRawStringListWithoutParenthesis(),"elemento1,elemento2,elemento3","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toEscapedStringList(),"('elemento1','elemento2','elemento3')","La stringa della lista di elementi non corrisponde a quello atteso!");
	}


	function testElementListWithMultipleParameters() {

		db('hosting_dreamhost_tests');

		$el = el('elemento1','elemento2','elemento3');			

		$this->assertEqual($el->toRawStringList(),"(elemento1,elemento2,elemento3)","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toRawStringListWithoutParenthesis(),"elemento1,elemento2,elemento3","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toEscapedStringList(),"('elemento1','elemento2','elemento3')","La stringa della lista di elementi non corrisponde a quello atteso!");
	}

	function testElementListWithArray() {

		db('hosting_dreamhost_tests');

		$el = el(['elemento1','elemento2','elemento3']);

		$this->assertEqual($el->toRawStringList(),"(elemento1,elemento2,elemento3)","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toRawStringListWithoutParenthesis(),"elemento1,elemento2,elemento3","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toEscapedStringList(),"('elemento1','elemento2','elemento3')","La stringa della lista di elementi non corrisponde a quello atteso!");

	}

	function testElementListWithArrayIntruder() {

		db('hosting_dreamhost_tests');

		try {

			$el = el('a','b',[],12);

			$this->fail("La lista viene creata anche quando contiene al suo interno un array!! Errore!!");
		}
		catch (\Exception $ex) {
			//ok
		}
	}

	function testEmptyElementList() {

		db('hosting_dreamhost_tests');

		try {
			$el = el();
			$this->fail("Non è stata lanciata un'eccezione per una lista di elementi vuota!");
		} catch (\Exception $ex) {
			//ok
		}

	}

	function testElementListWithEmptyArray() {

		db('hosting_dreamhost_tests');

		try {
			$el = el([]);
			$this->fail("Non è stata lanciata un'eccezione per una lista di elementi con dentro un array vuoto!");
		} catch (\Exception $ex) {
			//ok
		}

	}



}