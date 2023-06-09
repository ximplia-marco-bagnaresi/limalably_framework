<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJsonResponse extends LHttpResponse {

    private $my_result;

    function __construct($data) {
        $this->my_result = $data;
    }

    public function execute($format = null) {

        header("Content-Type: application/json; charset=utf-8");
        header("Content-Length: " . strlen($this->my_result));
        header("Connection: close");
        
        echo $this->my_result;

        Limalably::finish();
    }

}
