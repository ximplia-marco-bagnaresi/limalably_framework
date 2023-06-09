<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTreeMap implements ArrayAccess, Iterator {
    
    const SLASH_ESCAPE = '%';
    const FORBIDDEN_CHARACTER = '.';
    
    private $data=null;
    
    private $current_keys = null;
    private $current_index = -1;
    
    public function __construct(&$initial_data = array())
    {
        $this->data = &$initial_data;
    }    
    
    public static function path_tokens($path)
    {            
        if (strpos($path,self::FORBIDDEN_CHARACTER)!==false) throw new \Exception("The '".self::FORBIDDEN_CHARACTER."' is forbidden as path in TreeMap! Use TreeMapView!");
        
        $path_parts = explode("/",$path);
        
        $result = array();
        
        foreach ($path_parts as $p)
        {
            if ($p!=null) {
                
                $path_token = str_replace(self::SLASH_ESCAPE,'/',$p);
                
                $result[] = $path_token;
                
            }
        }
        return $result;
    }
    
    public static function all_but_last_path_tokens($path)
    {
        $path_tokens = self::path_tokens($path);
        
        if (count($path_tokens)>0) {
            array_pop($path_tokens);
            return $path_tokens;
        } else {
            return null;
        }
    }
    
    public static function last_path_token($path)
    {
        $path_tokens = self::path_tokens($path);
        if (count($path_tokens)>0) {
            return array_pop($path_tokens);
        } else {
            return null;
        }
    }
    
    public function getArray($path,$default_value) {
        $value = $this->get($path,$default_value);
        if (!is_array($value)) $value = array($value);
        return $value;
    }
    
    public function mustGetOriginal($path) {
        if (!$this->is_set($path))
            throw new \Exception('Value not found in path : '.$path);
        
        return $this->getOriginal($path);
    }
    
    public function getOriginal($path,$default_value = null) {
        if (!$this->is_set($path))
            return $default_value;
        
        $path_parts = self::path_tokens($path);
        
        $current_node = $this->data;
        foreach ($path_parts as $p)
        {
            $current_node = $current_node[$p];
        }
        
        return $current_node;
    }
    
    public function mustGetBoolean($path) {
        if (!$this->is_set($path))
            throw new \Exception('Value not found in path : '.$path);
        
        return $this->getBoolean($path);
    }
    
    /**
     * Ritorna un valore booleano o il valore di default nel caso in cui
     * @param type $path
     * @param type $default_value
     * @return boolean
     */
    public function getBoolean($path,$default_value = null) {
        if (!$this->is_set($path)) return $default_value;
        
        $value = $this->get($path,$default_value);
        
        $false_values = LConfig::mustGet('/defaults/treemap/false_values');
        if (in_array($value, $false_values)) return false;  //cerca nei valori, ok
        else return true;
    }
    
    private static function recursiveFilterVar(array $var_array) {
        foreach ($var_array as $k => $val) {
            if ($val === null || $val === false || $val === true || is_numeric($val)) continue;
            if (is_array($val)) {
                $var_array[$k] = self::recursiveFilterVar($val);
            } else {
                $var_array[$k] = filter_var($val);
            }
        }
        return $var_array;
    }
    
    function setRoot(&$value) {
        $this->data = $value;
    }
    
    function &getRoot() {
        return $this->data;
    }
    
    /*
     * Imposta un valore. L'ultima parte del path diventa la chiave.
     * Se il valore è un Tree viene creato un link.
     * Esempio :
     * 
     * path : /html/head/title
     * value = "Benvenuti nel sito XYZ!!"
     */
    function set($path,$value)
    {
        $path_used = self::all_but_last_path_tokens($path);
        
        $current_node = &$this->data;
        
        if ($path_used === null) {
            if ($value instanceof LTreeMap || $value instanceof LTreeMapView) //link
                $current_node = $value->get("/");//&$value->data;
            else
                $current_node = $value;
        } else {
            foreach ($path_used as $p)
            {            
                if (!array_key_exists($p,$current_node))
                    $current_node[$p] = array();

                $current_node = &$current_node[$p];

            }

            if ($value instanceof LTreeMap || $value instanceof LTreeMapView) //link
                $current_node[self::last_path_token($path)] = $value->get("/");//&$value->data;
            else
                $current_node[self::last_path_token($path)] = $value;
        }
    }
   
    /*
     * Aggiunge un valore all'array nella posizione corrente.
     * Se il valore è un albero viene creato un link.
     * Esempio :
     * 
     * path : /html/head/keywords
     * value : ravenna
     * 
     * Viene aggiunta "ravenna" alle keywords. Keywords deve essere un array.
     * 
     * 
     */
    function add($path,$value)
    {
        $path_parts = self::path_tokens($path);
        
        $current_node = &$this->data;
        
        foreach ($path_parts as $p)
        {
            if (!array_key_exists($p,$current_node))
                $current_node[$p] = array();
            if (!is_array($current_node[$p])) {
                $current_node[$p] = array($current_node[$p]);
            }
            $current_node = &$current_node[$p];
        }
        
        if (!is_array($current_node)) $current_node = array($current_node);
        if ($value instanceof LTreeMap || $value instanceof LTreeMapView)
            $current_node[] = $value->get("/");//&$value;
        else
            $current_node[] = $value;
    }
    
    /*
     * Effettua il merge di un'array di valori all'interno di un'altro array.
     * La differenza rispetto ad add sta nel fondere i due array.
     * Da usare se non si vogliono aggiungere i valori ad un array.
     */
    function merge($path,$value)
    {        
        if (!is_array($value)) $real_value = [$value];
        else $real_value = $value;

        $path_parts = self::path_tokens($path);
        
        $current_node = &$this->data;
        
        foreach ($path_parts as $p)
        {
            if (!array_key_exists($p,$current_node))
                $current_node[$p] = array();
            $current_node = &$current_node[$p];
        }
        
        $current_node = array_merge_recursive($current_node,$real_value);
    }
    
    function replace($path,$value) {
        if ($this->is_set($path)) $this->remove ($path);
        $this->set($path,$value);
    }
    
    /*
     * Rimuove le chiavi trovate nel path specificato.
     */
    function purge($path,$keys)
    {
        $path_parts = self::path_tokens($path);
        
        $current_node = &$this->data;
        
        foreach ($path_parts as $p)
        {
            if (!array_key_exists($p,$current_node))
                $current_node[$p] = array();
            $current_node = &$current_node[$p];
        }
        
        $current_node = array_diff($current_node,$keys);
    }
    
    function remove($path)
    {
        if (!$this->is_set($path)) return;
        if ($path=='/') $this->data = array();
        else
        {
            $path_parts = self::all_but_last_path_tokens($path);
            
            $current_node = &$this->data;

            foreach ($path_parts as $p)
            {
                $current_node = &$current_node[$p];
            }
                            
            unset($current_node[self::last_path_token($path)]);
        
        }
    }
      
    function mustGet($path) {
        if (!$this->is_set($path))
            throw new \Exception('Value not found in path : '.$path);
                
        return $this->get($path);        
    }
    
    /*
     * Ritorna il contenuto nella posizione specificata.
     * 
     * Es: 
     * path : /html/head/keywords
     * -> ritorna l'array delle keywords
     * 
     * path : /html/head/description
     * -> ritorna la descrizione
     */
    
    function get($path,$default_value=null)
    {
        if (!$this->is_set($path))
            return $default_value;
        
        $path_parts = self::path_tokens($path);
        
        $current_node = $this->data;
        foreach ($path_parts as $p)
        {
            if (!isset($current_node[$p])) return null;
            $current_node = $current_node[$p];
        }
        
        $return_value = $current_node;
        if ($return_value===null || $return_value===false || $return_value===true || is_numeric($return_value)) return $return_value;
        if (is_array($return_value)) {
            $return_value = self::recursiveFilterVar($return_value);
            return $return_value;
        }
        if (is_object($return_value)) return $return_value;
        else return filter_var($return_value,FILTER_DEFAULT);
    }
    
    /*
     * Crea una vista sul percorso specificato.
     * 
     */
    public function view($path)
    {
        return new LTreeMapView($path,$this);
    }
    
    /*
     * Ritorna true se un nodo dell'albero è stato definito, false altrimenti.
     */
    function is_set($path)
    {
        $path_parts = self::path_tokens($path);
        
        $current_node = $this->data;
        if (!is_array($current_node)) return true;
        foreach ($path_parts as $p)
        {
            if (!array_key_exists($p,$current_node))
                return false;

            $current_node = $current_node[$p];
        }
        
        return true;
    }

    public function has($path) {
        return $this->is_set($path);
    }

    /*
     * Ritorna tutte le chiavi trovate nella posizione specificata.
     *
     */
    public function keys($path)
    {
        if (!$this->is_set($path))
            return null;

        $path_parts = self::path_tokens($path);

        $current_node = $this->data;
        foreach ($path_parts as $p)
        {
            $current_node = $current_node[$p];
        }

        return array_keys($current_node);

    }

    //questa funzione va sistemata, deve pulire "dalla root" e usando remove.
    public function clear()
    {
        $this->data = array();
    }

    //array access
    
    public function offsetExists($offset) {
        return $this->is_set($offset);
    }

    public function offsetGet($offset) {
        return $this->mustGet($offset);
    }

    public function offsetSet($offset, $value) {
        $this->set($offset,$value);
    }

    public function offsetUnset($offset) {
        $this->remove($offset);
    }

    public function current() {
        return $this->get($this->current_keys[$this->current_index]);
    }

    public function key(){
        return $this->current_keys[$this->current_index];
    }

    public function next() {
        $this->current_index++;
    }

    public function rewind() {
        $this->current_keys = $this->keys('/');
        $this->current_keys[] = null;
        $this->current_index = 0;
    }

    public function valid() {
        return isset($this->current_keys[$this->current_index]);
    }

}
