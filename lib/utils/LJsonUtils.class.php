<?php

class LJsonUtils {
    
    static function parseContent($object_name,$path,$content) {
        $result_array = json_decode($content,true);
        $last_error = json_last_error();
        if ($last_error == JSON_ERROR_NONE) {
            return $result_array;
        }
        switch ($last_error) {
            case JSON_ERROR_DEPTH : throw new \Exception("Error decoding ".$object_name." at path : ".$path.". Max depth reached.");
            case JSON_ERROR_STATE_MISMATCH : throw new \Exception("Error decoding ".$object_name." at path : ".$path.". Invalid or malformed JSON.");
            case JSON_ERROR_CTRL_CHAR : throw new \Exception("Error decoding ".$object_name." at path : ".$path.". Control character error.");
            case JSON_ERROR_SYNTAX : throw new \Exception("Error decoding ".$object_name." at path : ".$path.". Syntax error.");
            case JSON_ERROR_UTF8 : throw new \Exception("Error decoding ".$object_name." at path : ".$path.". UTF-8 encoding error.");
            case JSON_ERROR_RECURSION : throw new \Exception("Error decoding ".$object_name." at path : ".$path.". Error in recursive references.");
            case JSON_ERROR_INF_OR_NAN : throw new \Exception("Error decoding ".$object_name." at path : ".$path.". INF or NaN values found.");
            case JSON_ERROR_UNSUPPORTED_TYPE : throw new \Exception("Error decoding ".$object_name." at path : ".$path.". A value of type that cannot be encoded is found.");
            case JSON_ERROR_INVALID_PROPERTY_NAME : throw new \Exception("Error decoding ".$object_name." at path : ".$path.". Invalid property name.");
            case JSON_ERROR_UTF16 : throw new \Exception("Error decoding ".$object_name." at path : ".$path.". UTF-16 encoding error.");
            default : throw new \Exception("Unrecognized error decoding ".$object_name." at path : ".$path.".");
        }
    }
    
    static function encodeData($object_name,$path,$data) {
        $result_content = json_encode($data);
        $last_error = json_last_error();
        if ($last_error == JSON_ERROR_NONE) {
            return $result_content;
        }
        switch ($last_error) {
            case JSON_ERROR_DEPTH : throw new \Exception("Error encoding ".$object_name." at path : ".$path.". Max depth reached.");
            case JSON_ERROR_STATE_MISMATCH : throw new \Exception("Error encoding ".$object_name." at path : ".$path.". Invalid or malformed JSON.");
            case JSON_ERROR_CTRL_CHAR : throw new \Exception("Error encoding ".$object_name." at path : ".$path.". Control character error.");
            case JSON_ERROR_SYNTAX : throw new \Exception("Error encoding ".$object_name." at path : ".$path.". Syntax error.");
            case JSON_ERROR_UTF8 : throw new \Exception("Error encoding ".$object_name." at path : ".$path.". UTF-8 encoding error.");
            case JSON_ERROR_RECURSION : throw new \Exception("Error encoding ".$object_name." at path : ".$path.". Error in recursive references.");
            case JSON_ERROR_INF_OR_NAN : throw new \Exception("Error encoding ".$object_name." at path : ".$path.". INF or NaN values found.");
            case JSON_ERROR_UNSUPPORTED_TYPE : throw new \Exception("Error encoding ".$object_name." at path : ".$path.". A value of type that cannot be encoded is found.");
            case JSON_ERROR_INVALID_PROPERTY_NAME : throw new \Exception("Error encoding ".$object_name." at path : ".$path.". Invalid property name.");
            case JSON_ERROR_UTF16 : throw new \Exception("Error encoding ".$object_name." at path : ".$path.". UTF-16 encoding error.");
            default : throw new \Exception("Unrecognized error encoding ".$object_name." at path : ".$path.".");
        }
    }
    
}