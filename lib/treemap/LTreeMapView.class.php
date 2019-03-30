<?php

class LTreeMapView implements ArrayAccess, Iterator {
    
    private $view_map;
    private $view_prefix;

    function __construct($prefix,$treemap)
    {
        $this->view_map = $treemap;
        $this->view_prefix = $prefix.'/';
    }

    function getBoolean($path,$default_value=null) {
        return $this->view_map->getBoolean($this->view_prefix.$path,$default_value);
    }
    
    function mustGetBoolean($path) {
        return $this->view_map->mustGetBoolean($this->view_prefix.$path);
    }
    
    function mustGetOriginal($path) {
        return $this->view_map->mustGetOriginal($this->view_prefix.$path); 
    }
    
    function getOriginal($path,$default_value = null) {
        return $this->view_map->getOriginal($this->view_prefix.$path,$default_value);
    }
    
    function set($path,$value)
    {
        $this->view_map->set($this->view_prefix.$path,$value);
    }

    /*
     * Crea una vista sul percorso specificato.
     *
     */
    public function view($path)
    {
        return new LTreeMapView($this->view_prefix.$path,$this->view_map);
    }

    /*
     * Aggiunge un valore nella posizione corrente.
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
        $this->view_map->add($this->view_prefix.$path,$value);
    }

    /*
     * Effettua il merge di un'array di valori all'interno di un'altro array.
     * La differenza rispetto ad add sta nel fondere i due array.
     */
    function merge($path,$value)
    {
        $this->view_map->merge($this->view_prefix.$path,$value);
    }

    /*
     * Rimuove le chiavi trovate nel path specificato.
     */
    function purge($path,$keys)
    {
        $this->view_map->purge($this->view_prefix.$path,$keys);
    }

    function remove($path)
    {
        $this->view_map->remove($this->view_prefix.$path);
    }

    function mustGet($path) {
        return $this->view_map->mustGet($this->view_prefix.$path);
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
        return $this->view_map->get($this->view_prefix.$path,$default_value);
    }

    /*
     * Ritorna true se un nodo dell'albero è stato definito, false altrimenti.
     */
    function is_set($path)
    {
        return $this->view_map->is_set($this->view_prefix.$path);
    }

    function keys($path) {
        return $this->view_map->keys($this->view_prefix.$path);
    }
    
    //array access
    
    public function offsetExists($path): bool {
        return $this->view_map->is_set($this->view_prefix.$path);
    }

    public function offsetGet($path) {
        return $this->view_map->mustGet($this->view_prefix.$path);
    }

    public function offsetSet($path, $value): void {
        $this->view_map->set($this->view_prefix.$path,$value);
    }

    public function offsetUnset($path): void {
        $this->view_map->remove($this->view_prefix.$path);
    }
    
    public function current() {
        return $this->get($this->current_keys[$this->current_index]);
    }

    public function key() {
        return $this->current_keys[$this->current_index];
    }

    public function next(): void {
        $this->current_index++;
    }

    public function rewind(): void {
        $this->current_keys = $this->view_map->keys($this->view_prefix);
        $this->current_keys[] = null;
        $this->current_index = 0;
    }

    public function valid(): bool {
        return isset($this->current_keys[$this->current_index]);
    }
}