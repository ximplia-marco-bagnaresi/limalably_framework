<?php

class LHttpError extends LHttpResponse {
    
    const ERROR_BAD_REQUEST = '400';
    const ERROR_UNAUTHORIZED = '401';
    const ERROR_FORBIDDEN = '403';
    const ERROR_PAGE_NOT_FOUND = '404';
    const ERROR_METHOD_NOT_ALLOWED = '405';
    const ERROR_IM_A_TEAPOT = '418';
    const ERROR_TOO_MANY_REQUESTS = '429';
    const ERROR_INTERNAL_SERVER_ERROR = '500';
    const ERROR_NOT_IMPLEMENTED = '501';
    const ERROR_SERVICE_UNAVAILABLE = '503';
    
    
    private $error_code;
    
    function __construct($error_code) {
        $this->error_code = $error_code;
        parent::__construct("Http error ".$error_code);
    }
    
    function execute($format = null) {
        
        if ($format == null || $format == LFormat::DATA) $format = LConfigReader::simple('/format/default_error_format');
        
        //http_response_code($this->error_code);
        
        $error_templates_folder = LConfigReader::executionMode('/format/'.$format.'/error_templates_folder');
        
        $template_source_factory_class_name = LConfigReader::simple('/template/source_factory_class');
        
        $factory_instance = new $template_source_factory_class_name();
        
        $cache_folder = LConfigReader::simple('/template/cache_folder');
        
        $file_source = $factory_instance->createFileTemplateSource($error_templates_folder,$cache_folder);
        
        $template_path = $file_source->searchTemplate($this->error_code);
                
        if ($template_path) {
            
            $template = $file_source->getTemplate($template_path);
            
            $output = new LTreeMap();
            
            LWarningList::mergeIntoTreeMap($output);
            LErrorList::mergeIntoTreeMap($output);
            
            $output_root_array = $output->getRoot();
            
            echo $template->render($output_root_array);
        } else {
            echo "HTTP error ".$this->error_code.".\n";
        }
         
        Lym::finish();
    }
    
}