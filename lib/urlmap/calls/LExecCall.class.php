<?php

class LExecCall {
    
    const REPLACE_DATA_CALL_OPTION_SUFFIX = '!';
    
    private $my_call = null;
    
    function isInitialized() {
        return $this->my_call->isInitialized();
    }
    
    public function init($base_dir,$proc_folder,$proc_extension,$data_folder) { 
        $this->my_call->init($base_dir,$proc_folder,$proc_extension,$data_folder);
    }
    
    public function initWithDefaults() {
        $this->my_call->initWithDefaults();
    }
    
    function __construct() {
        $this->my_call = new LCall();
    }
    
    public function execute(string $call_spec,array $all_param_data) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (LStringUtils::endsWith($call_spec,self::REPLACE_DATA_CALL_OPTION_SUFFIX)) {
            $use_replace = true;
            $my_call_spec = substr($call_spec,0,-1);
        } else {
            $use_replace = false;
            $my_call_spec = $call_spec;
        }
                
        $result = $this->my_call->execute($my_call_spec,$all_param_data,false);
        
        if ($result instanceof \LErrorList) return $result;
        
        if ($result instanceof LTreeMap) {
            $result = $result->get('/');
        }
        
        if ($result instanceof LTreeMapView) {
            $result = $result->get('.');
        }
        
        $my_output = $all_param_data['output'];
        
        if ((!$my_output instanceof LTreeMap) && (!$my_output instanceof LTreeMapView)) throw new \Exception("A TreeMap or TreeMapView is needed for output!");
        
        if ($my_output instanceof LTreeMap) $my_output_path = '/';
        if ($my_output instanceof LTreeMapView) $my_output_path = '.';
        
        if ($use_replace) {
            $all_param_data['output']->replace($my_output_path,$result);
        } else {
            $all_param_data['output']->merge($my_output_path,$result);
        }
        
        return null;
    }
    
    
}