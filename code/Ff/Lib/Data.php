<?php

namespace Ff\Lib;

class Data extends \stdClass
{
    public function __construct(array $data = array(), $locked = true)
    {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    public function __set($path, $value)
    {
        if (strpos($path, '/') === false) {
            if (is_array($value)) {
                $this->$path = new Data($value);
            } else {
                $this->$path = $value;
            }
        } else {
            $pathArray = explode('/', $path);
            if ($pathArray) {
                $object = $this;
                $prev = $this;
                foreach ($pathArray as $key) {
                    $prev = $object;
                    if (isset($object->$key)) {
                        $object = $object->$key;
                    } else {
                        $object->$key = new Data();
                        $object = $object->$key;
                    }
                    // @TODO
                }
                $prev->$key = $value;
            }
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

    public function __toString()
    {
        return '[Data Object]';
    }

    public function extend(Data $data)
    {

    }
    
    private function getByPath($path, $default)
    {
        $pathArray = explode('/', $path);

        $element = $this;
        foreach ($pathArray as $key) {
            if ($element instanceof \stdClass ) {
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
