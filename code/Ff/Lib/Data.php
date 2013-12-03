<?php

namespace Ff\Lib;

class Data extends \stdClass
{
    private $locked = true;
    
    public function __construct(array $data = array(), $locked = true)
    {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
        
        $this->locked = $locked;
    }
    
    public function __clone()
    {
        $this->locked = false;
    }

    public function __set($key, $value)
    {
        if (is_array($value)) {
            $this->$key = new Data($value);
        } else {
            $this->$key = $value;
        }
    }
    
    public function __get($key)
    {
        return isset($this->$key) ? $this->$key : null;
    }
    
    public function get($key, $default = null)
    {
        if (strpos($key, '/') === false) {
            return isset($this->$key) ? $this->$key : $default;
        }
        
        return $this->getByPath($key, $default);
    }
    
    protected function getByPath($path, $default)
    {
        $pathArray = explode('/', $path);

        $element = $this;
        foreach ($pathArray as $key) {
            if (is_array($element)) {
                if (isset($element[$key])) {
                    $element = $element[$key];
                } else {
                    return $default;
                }
            } else if ($element instanceof \stdClass ) {
                if (isset($element->$key)) {
                    $element = $element->$key;
                } else {
                    return $default;
                }
            } else {
                throw new \InvalidArgumentException("Wrong path argument.");
            }
        }

        return $element;
    }
}
