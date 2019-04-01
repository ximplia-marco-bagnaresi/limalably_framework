<?php

class LDataStorage {
    
    private $root_path = null;
    
    function isInitialized() {
        return $this->root_path != null;
    }
    
    function initWithDefaults() {
        $this->root_path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/data_folder');
    }
    
    function init($root_path) {
        $this->root_path = $root_path;
    }
    
    function get(string $path) {
        $my_path1 = $this->root_path.$path.'.json';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        $content = file_get_contents($my_path1);
        
        return LJsonUtils::parseContent("data file",$path,$content);
    }
    
    function is_set(string $path) {
        $my_path1 = $this->root_path.$path.'.json';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        //add xml support
        
        return is_file($my_path1);
    }
    
    function set(string $path,array $data) {
        
        $my_path1 = $this->root_path.$path.'.json';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        $my_dir = dirname($my_path1);
        if (!is_dir($my_dir)) {
            mkdir($my_dir, 0777, true);
            chmod($my_dir, 0777);
        }
        
        $content = LJsonUtils::encodeData("data file",$path,$data);
        
        file_put_contents($my_path1, $content, LOCK_EX);
    }
    
    
}