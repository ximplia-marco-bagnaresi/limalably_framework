<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTag implements LITagRenderingTips, ArrayAccess
{
    private $my_parent = null;
    private $required_attributes = array();
    private $attributes = array();
    private $required_children = array();
    private $children = array();

    private $original_tag_name = null;
    private $tag_name = null;
    private $tag_mode = self::TAG_MODE_AUTO;
    private $indent_mode = self::INDENT_MODE_AUTO;

    private static $indent_level = 0;

    function __construct(string $tag_name) {
        $this->original_tag_name = $tag_name;
        $this->tag_name = $tag_name;

        if (LHtmlStandardTagTable::hasTagDefinition($tag_name)) {
            LHtmlStandardTagTable::setup($tag_name,$this);
        }
    }

    //setup and dump functions

    public function setTagName(string $tag_name) {
        $this->tag_name = $tag_name;

        return $this;
    }

    public function setTagMode($tag_mode) {
        $this->tag_mode = $tag_mode;

        return $this;
    }

    public function setParent($parent) {
        $this->my_parent = $parent;
    }

    public function getParent() {
        return $this->my_parent;
    }

    public function setIndentMode($indent_mode) {
        $this->indent_mode = $indent_mode;

        return $this;
    }

    private function checkIndentMode() {
        if ($this->tag_mode == self::TAG_MODE_OPEN_ONLY || $this->tag_mode== self::TAG_MODE_OPEN_EMPTY_CLOSE || $this->tag_mode==self::TAG_MODE_OPENCLOSE_NO_CONTENT) {
            if ($this->indent_mode != self::INDENT_MODE_SKIP_ALL) throw new \Exception("Required intent mode for ".$this->getPrintableTagMode()." is INDENT_MODE_SKIP_ALL.");
        }
    }

    private function getPrintableTagMode() {
        switch ($this->tag_mode) {
            case self::TAG_MODE_AUTO : return 'TAG_MODE_AUTO';
            case self::TAG_MODE_OPEN_CONTENT_CLOSE : return 'TAG_MODE_OPEN_CONTENT_CLOSE';
            case self::TAG_MODE_OPEN_EMPTY_CLOSE : return 'TAG_MODE_OPEN_EMPTY_CLOSE';
            case self::TAG_MODE_OPEN_ONLY : return 'TAG_MODE_OPEN_ONLY';
            case self::TAG_MODE_OPENCLOSE_NO_CONTENT : return 'TAG_MODE_OPENCLOSE_NO_CONTENT';

            default : return "Unknown mode : fix the code.";
        }
    }

    /**
    Non è detto che in futuro non crei semplicemente una tabella di nomi di elementi di cui fare il replace in toto
    in modo da usare comunque il singolo underscore per il trattino medio (-).
    Comunque anche questa non è male come soluzione. Non è detto che non si possano usare entrambe.
    */
    private function realElementName($name) {
        $step1 = str_replace('__','-',$name);
        $step2 = str_replace('§','__',$step1);

        return $step2;
    }
    
    //attributes management

    function __call($method_name,$parameters) {

        if ($this->tag_mode == self::TAG_MODE_AUTO) throw new \Exception("Mode is not correctly setup!");

        if (count($parameters)==0) {
            $this->setAttribute($method_name,false);
            return $this;
        }

        if ($method_name=='class' || $method_name=='style') {

            if ($method_name=='class')
                $char = ' ';
            if ($method_name=='style')
                $char = ';';

            foreach ($parameters as $p)
            {
                $this->pushCharSeparatedAttributeValue($method_name,$char,$p);
            }

            return $this;
        } else {
            if (count($parameters)!=1) throw new \Exception("Invalid number of values for attribute ".$method_name);

            $this->setAttribute($method_name,$parameters[0]);
        
            return $this;
        }

    }

    function __isset($key) {

        $real_key = $this->realElementName($key);

        if (isset($this->attributes[$real_key])) return true;
        if (isset($this->children[$real_key])) return true;

        return false;
    }

    function __unset($key) {

        $real_key = $this->realElementName($key);

        if (isset($this->attributes[$real_key])) {
            unset($this->attributes[$real_key]);
        }        

    }

    function setAttribute($key,$value)
    {
        $this->attributes[$this->realElementName($key)] = $value;
    }

    function getAttribute($key) {

        $key = $this->realElementName($key);

        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    function pushCharSeparatedAttributeValue($key,$char,$value) {
        if (strlen($char)!=1) throw new \Exception("Separator is not a single char. Found : ".$char);

        $current_value = $this->getAttribute($key);

        if (!$current_value) {
            $current_value = $value;
        }
        else {
            $current_value .= $value;
        }

        if (!LStringUtils::endsWith($current_value,$char)) $current_value .= $char;

        $this->setAttribute($key,$current_value);
    }

    private function renderAttribute($key,$value) {

        if (is_object($value)) $value = "".$value;

        if ($value===false) return ' '.$key;

        $result = ' '.$key.'="';
        if ($value==null) $value="";
        $result .= str_replace('"',"'",$value);
        $result .= '"';

        return $result; 
    }

    public function addRequiredAttribute($attr_name) {
        $this->required_attributes[] = $this->realElementName($attr_name);
    }

    private function checkRequiredAttributes() {
        foreach ($this->required_attributes as $attr_name) {
            if (!isset($this->attributes[$attr_name])) throw new \Exception("Missing required attribute '".$attr_name."' for ".$this->original_tag_name);
        }
    }

    function hasAttribute($key)
    {
        return isset($this->attributes[$this->realElementName($key)]);
    }

    //child management

    private function parentedChild($child) {
        if ($child instanceof LTag) {
            $child->setParent($this);
        }

        return $child;
    } 

    public function add(... $child)
    {
        if ($this->tag_mode == self::TAG_MODE_AUTO) throw new \Exception("Mode is not correctly setup!");
        if ($this->tag_mode != self::TAG_MODE_OPEN_CONTENT_CLOSE) throw new \Exception("Tag mode not valid for add child : ".$this->getPrintableTagMode());

        foreach ($child as $c)
        {
            $this->setChild(null,$c);
        }

        return $this;
    }

    function getChildren()
    {
        return $this->children;
    }

    function getChild($child_name) {

        $child_name = $this->realElementName($child_name);

        if (isset($this->children[$$child_name])) return $this->children[$child_name];
        else {
            if (in_array($child_name,$this->required_children)) throw new \Exception("Missing children ".$child_name." in tag ".$this->original_tag_name);
            else return "<!-- empty child '".$child_name."' -->";
        }
    }

    function setChild($offset,$child) {
        if (is_null($child)) throw new \Exception("Unable to add null to this child list");

        if (is_null($offset)) {
            $this->children[] = $this->parentedChild($child);
        } else {
            if (is_string($offset)) $offset = $this->realElementName($offset);

            $this->children[$offset] = $this->parentedChild($child);
        }
    }

    function hasChild($child_name) {

        $child_name = $this->realElementName($child_name);

        return isset($this->children[$child_name]);
    }

    public function addRequiredChild($child_name) {

        $child_name = $this->realElementName($child_name);

        $this->required_children[] = $child_name;
    }

    private function checkRequiredChildren() {
        foreach ($this->required_children as $child_name) {
            if (!isset($this->children[$child_name])) throw new \Exception("Missing required children '".$child_name."' for ".$this->original_tag_name);
        }
    }

    public function findAncestorChildByName($child_name) {

        $child_name = $this->realElementName($child_name);

        if (isset($this->children[$child_name])) return $this->children[$child_name];

        if (in_array($child_name,$this->required_children)) throw new \Exception("Required children is missing from ".$this->original_tag_name);

        $parent = $this->my_parent;

        if ($parent==null) throw new \Exception("Missing unknown child ".$child_name);
        else return $parent->findAncestorChildByName($child_name);
    }

    /**
     * ArrayAccess interface
    */
    public function offsetExists($offset) {

        if (is_string($offset)) $offset = $this->realElementName($offset);

        return isset($this->children[$offset]);
    }

    /**
     * ArrayAccess interface
    */
    public function offsetGet($child_name) {

        return $this->getChild($child_name);
    }

    /**
     * ArrayAccess interface
    */
    public function offsetSet($offset,$child) {

        $this->setChild($offset,$child);
    }

    /**
     * ArrayAccess interface
    */
    public function offsetUnset($offset) {

        if (is_string($offset)) $offset = $this->realElementName($offset);

        unset($this->children[$offset]);
    }

    //rendering

    function __toString() {
        if (!$this->tag_name) throw new \Exception("Tag name is not set.");
        if ($this->tag_mode == self::TAG_MODE_AUTO) throw new \Exception("Can't render tag with TAG_MODE_AUTO set.");
        if ($this->indent_mode == self::INDENT_MODE_AUTO) throw new \Exception("Can't render tag with INDENT_MODE_AUTO set.");

        $this->checkIndentMode();
        $this->checkRequiredAttributes();
        $this->checkRequiredChildren();

        $result = "";
        if ($this->indent_mode == self::INDENT_MODE_NORMAL) {
            for ($i=0;$i<self::$indent_level;$i++) $result .= "\t";
        }

        $result = '<'.$this->tag_name;

        foreach ($this->attributes as $key => $value) {
            $result.= $this->renderAttribute($key, $value);
        }

        switch ($this->tag_mode) {
            case self::TAG_MODE_OPEN_CONTENT_CLOSE : {

                $result .= " >";

                if ($this->indent_mode == self::INDENT_MODE_NORMAL) 
                    {
                        $result .= "\r\n";
                        self::$indent_level++;
                    }

                foreach ($this->children as $key => $child) {
                    if (is_numeric($key)) {
                        if ($this->indent_mode == self::INDENT_MODE_NORMAL) { 
                            for ($i=0;$i<self::$indent_level;$i++) $result .= "\t";
                        }

                        $result.= "".$child;
                    }
                }

                if ($this->indent_mode == self::INDENT_MODE_NORMAL) $result .= "\r\n";

                if ($this->indent_mode == self::INDENT_MODE_NORMAL) self::$indent_level--;

                if ($this->indent_mode == self::INDENT_MODE_NORMAL) {
                    for ($i=0;$i<self::$indent_level;$i++) $result .= "\t";
                }

                $result .= '</'.$this->tag_name.'>';
                

                return $result;
            }

            case self::TAG_MODE_OPEN_EMPTY_CLOSE : {

                $result .= " >";
                $result .= '</'.$this->tag_name.'>';

                return $result;
            }

            case self::TAG_MODE_OPEN_ONLY : {
                $result .= " >";

                return $result;
            }

            case self::TAG_MODE_OPENCLOSE_NO_CONTENT : {
                $result .= " />";

                return $result;
            }
        }
    }

}

?>