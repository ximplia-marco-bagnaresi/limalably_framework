<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTogetherFileLogger implements LILogger {
        
    const TOGETHER_FILENAME = 'log.all.txt';
    
    private $initialized;
    
    private $my_log_writer = null;
    
    function __construct($log_dir,$format_info,$log_mode,$max_mb=10) {
        
        $this->my_log_writer = new LFileLogWriter($log_dir, self::TOGETHER_FILENAME, $format_info,$log_mode,$max_mb);
    }
    
    public function isInitialized() {
        return $this->initialized;
    }
    
    public function init() {
        $this->initialized = true;
        
        $this->my_log_writer->init();
    }
    
    public function debug($message) {
        $this->my_log_writer->write($message, self::LEVEL_DEBUG);
    }

    public function error($message,$code = '') {
        $this->my_log_writer->write($message, self::LEVEL_ERROR,$code);
    }

    public function exception(\Exception $ex) {
        $this->my_log_writer->write(LStringUtils::getExceptionMessage($ex), self::LEVEL_ERROR,$ex->getCode());
    }

    public function fatal($message) {
        $this->my_log_writer->write($message, self::LEVEL_FATAL);
    }

    public function info($message) {
        $this->my_log_writer->write($message, self::LEVEL_INFO);
    }

    public function warning($message) {
        $this->my_log_writer->write($message, self::LEVEL_WARNING);
    }
    
    public function close() {
        $this->my_log_writer->close();
    }

}
