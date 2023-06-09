<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LSqliteConnection implements LIDbConnection {
    
    private $name = null;
    private $params = null;
    
    private $is_open = false;
    private $my_handle = null;
    
    function __construct(string $name,$params) {
        $this->name = $name;
        $this->params = $params;
    }

    public function getName() {
        return $this->name;
    }
    
    public function close() {
        if ($this->is_open) {
            $this->my_handle = null;
            $this->is_open = false;
        }
    }

    public function getHandle() {
        if ($this->my_handle) {
            return $this->my_handle;
        } else throw new \Exception("Invalid connection, can't get handle.");
    }

    public function isOpen() {
        return $this->is_open;
    }

    public function getConnectionString() {
        
        if (!isset($this->params['file'])) throw new \Exception("The path of the sqlite database file is not set with the 'file' parameter");
        
        $file_path = $this->params['file'];
        if (!LStringUtils::startsWith($file_path, '/')) {
            $file_path = LConfig::mustGet('PROJECT_DIR').$file_path;
        }
        
        return 'sqlite:'.$file_path;
    }
    
    public function open() {
        try {
            $result = new PDO($this->getConnectionString($this->params));
            
            $this->my_handle = $result;
            return true;
            
        } catch (\Exception $ex) {
            LResult::exception($ex);
            return false;
        }
    }

    public function beginTransaction() {
        throw new \Exception("Not implemented yet!");
    }

    public function rollback() {
        throw new \Exception("Not implemented yet!");
    }

    public function commit() {
        throw new \Exception("Not implemented yet!");
    }

    public function setCharset($charset_name) {
        throw new \Exception("Not implemented yet!");   
    }

}
