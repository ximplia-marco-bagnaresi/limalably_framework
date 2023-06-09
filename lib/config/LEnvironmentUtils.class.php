<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LEnvironmentUtils {
    
    public static function getServerUser() {
        
        return isset($_SERVER['USER']) ? $_SERVER['USER'] : (isset($_ENV['APACHE_RUN_USER']) ? $_ENV['APACHE_RUN_USER'] : 'apache' );
    }
    
    public static function getRemoteIp() {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }
    
    public static function getPreferredLanguageArray() {
        if (isset($_SERVER['LANG'])) {
            $lang_parts = explode('.',$_SERVER['LANG']);
            return [$lang_parts[0]];
        }
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang_corrected = str_replace('-', '_', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $lang_corrected = str_replace(',',';',$lang_corrected);
            $lang_parts = explode(';',$lang_corrected);
            $langs_array = [];
            $current_langs = [];
            foreach ($lang_parts as $lang_tok) {
                if (strpos($lang_tok, 'q=')===0) {
                    $i_val = 10-(substr($lang_tok,2)*10);
                    $langs_array[$i_val] = $current_langs;
                    $current_langs = [];
                } else {
                    $current_langs[] = $lang_tok;
                }
            }
            ksort($langs_array);
            $final_result = [];        
            foreach ($langs_array as $k => $val_array) {
                foreach ($val_array as $val) {
                    array_push($final_result, $val);
                }
            }
            return $final_result;
        }
        return ["default"];
    }
    
    public static function getRequestTime() {
        return $_SERVER['REQUEST_TIME'];
    }
    
    public static function getRequestDateTimeString() {
        return date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
    }
    
    public static function getHostname() {
        return $_SERVER['HOSTNAME'];
    }
    
    public static function getEnvironment() {
        return $_SERVER['ENVIRONMENT'];
    }
    
    public static function getRawRoute() {
        return $_SERVER['RAW_ROUTE'];
    }
    
    public static function getRoute() {
        return $_SERVER['ROUTE'];
    }
    
    public static function getFrameworkDir() {
        return $_SERVER['FRAMEWORK_DIR'];
    }
    
    public static function hasProjectDir() {
        return isset($_SERVER['PROJECT_DIR']);
    }
    
    public static function getProjectDir() {
        return $_SERVER['PROJECT_DIR'];
    }
    
    public static function getBaseDir() {
        return isset($_SERVER['PROJECT_DIR']) ? $_SERVER['PROJECT_DIR'] : $_SERVER['FRAMEWORK_DIR'];
    }
    
    public static function getRequestMethod() {
        if (isset($_SERVER['REQUEST_METHOD'])) return $_SERVER['REQUEST_METHOD'];
        else return 'CLI';
    }
    
    public static function getBuildNumber() {
        if (LExecutionMode::isFrameworkDevelopment() || LExecutionMode::isDevelopment()) {
            return "t".time();
        } else {
            return LConfigReader::simple('/misc/build');
        }
    }
    
    public static function getReplacementsArray() {
      
        return array('request_method' => strtolower(LEnvironmentUtils::getRequestMethod()),
                    'execution_mode' => LExecutionMode::get(),
                    'exec_mode' => LExecutionMode::getShort(),
                    'environment' => LEnvironmentUtils::getEnvironment(),
                    'hostname' => LEnvironmentUtils::getHostname(),
                    'language' => LI18nUtils::getCurrentLang(),
                    'build' => LEnvironmentUtils::getBuildNumber(),
                    'route' => LEnvironmentUtils::getRoute(),
                    'raw_route' => LEnvironmentUtils::getRawRoute()
            );
    
    }
}