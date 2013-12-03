<?php

namespace Ff\Lib;

use Ff\Api\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var \SimpleXMLElement
     */
    protected $config;

    public function __construct()
    {
        //
    }
    
    public function load($filePath = null)
    {
        if ($filePath === null) {
            $filePath = 'etc/configuration.xml';
        }
        $this->config = simplexml_load_file($filePath);
    }

    public function get($path)
    {
        $element = $this->findElement($path);
        if ($element) {
            $result = $this->convert($element);
        } else {
            $result = null;
        }
        return $result;
    }

    public function findElement($path)
    {
        if ($path === null) {
            return $this->config;
        }
        $pathArr = explode('/', $path);

        $element = $this->config;
        foreach ($pathArr as $nodeName) {
            $element = $element->$nodeName;
            if (!$element) {
                return false;
            }
        }
        return $element;
    }

    protected function convert(\SimpleXMLElement $element)
    {
        $result = null;

        if ($this->hasChildren($element)) {
            $result = array();
            foreach ($element->children() as $childName => $child) {
                $result[$childName] = $this->convert($child);
            }
        } else {
            $result = (string) $element;
        }

        return $result;
    }

    protected function hasChildren(\SimpleXMLElement $element)
    {
        if (!$element->children()) {
            return false;
        }

        foreach ($element->children() as $k => $child) {
            return true;
        }

        return false;
    }
}
