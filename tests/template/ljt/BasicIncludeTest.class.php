<?php


class BasicIncludeTest extends LTestCase {
	

	function setUp() {

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/template/ljt/fake_project/';

		LConfig::reset();
		LConfig::init();

	}

	function testIncludePhp() {
		
		$obj = new IncludePhpTemplate();

		$obj->setTreeDataPosition("/LAYOUT/some/random/position");

		$obj->parse(['vars' => ['planet' => 'world'],'template' => 'php/hello']);

		$this->assertEqual("".$obj,"Hello world!","Il rendering dell'include del template non è andato a buon fine!");
	}
	
	public function testTagLib() {

		$template = new LJTemplate('{}');

		$hello = tag('hello');

		$this->assertEqual('<hello prova="ciao" ></hello>',"".$hello,"Il rendering del tag non corrisponde!");

	}

	function testIncludeLjt() {
		$obj = new IncludeLjtTemplate();

		$obj->setTreeDataPosition("/LAYOUT/some/random/position");

		$obj->parse(['template' => 'ljt/hello']);

		$this->assertEqual("".$obj,"Hello world!","Il rendering dell'include del template non è andato a buon fine!");
	}
	

	/*
	function testIncludeTwig() {
		$obj = new IncludeTwigTemplate();

		$obj->setTreeDataPosition("/LAYOUT/some/random/position");

		$obj->parse(['vars' => ['planet' => 'world'],'template' => 'twig/hello']);

		$this->assertEqual("".$obj,"Hello world!","Il rendering dell'include del template non è andato a buon fine!");
	}
	*/

	function tearDown() {
		unset($_SERVER['PROJECT_DIR']);
	}


}