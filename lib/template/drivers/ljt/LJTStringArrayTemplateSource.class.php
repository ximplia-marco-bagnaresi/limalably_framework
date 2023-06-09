<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJTStringArrayTemplateSource implements LITemplateSource {
		
	private $engine_name;
	private $data_map;

	function __construct(string $engine_name,$data_map) {

		$this->engine_name = $engine_name;
		$this->data_map = $data_map;

	}

    function searchTemplate($path) {
    	if (isset($this->data_map[$path])) return $path;
    	else return false;
    }

    function hasRootFolder() {
    	return false;
    }

    function getRootFolder() {
    	return null;
    }
    
    function getTemplate($path) {

    	$content = $this->data_map[$path];

    	return new LJTemplate($path,$content);

    }
}