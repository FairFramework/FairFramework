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
        if (!$path) {
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

    public function toStructure(\SimpleXMLElement $element = null, $uri = '')
    {
        if ($element === null) {
            $element = $this->config;
            $uri = $element->getName();
        } else {
            $uri .= '/' . $element->getName();
        }

        $result = new Data();
        $result->uri = $uri;
        if ($element->attributes()) {
            foreach ($element->attributes() as $attributeName => $attribute) {
                $result->$attributeName = (string) $attribute;
            }
        }
        $isCollection = isset($result->type) && ($result->type === 'collection');
        if ($isCollection && $this->hasChildren($element)) {
            $result->items = new Data();
            foreach ($element->children() as $childName => $child) {
                $result->items->$childName = $this->toStructure($child, $uri);
            }
        } elseif ($this->hasChildren($element))  {
            foreach ($element->children() as $childName => $child) {
                $result->$childName = $this->toStructure($child, $uri);
            }
        } elseif ($element->attributes()) {
            $result->value = (string) $element;
        } else {
            $result = (string) $element;
        }

        return $result;
    }

    public function extend(Configuration $configuration, $overwrite = false)
    {
        $rootElement = $configuration->findElement(null);
        foreach ($rootElement->children() as $child) {
            $this->extendElement($this->config, $child, $overwrite);
        }

        return $this;
    }

    private function extendElement(\SimpleXMLElement $target, \SimpleXMLElement $element, $overwrite = false)
    {
        $elementName = $element->getName();
        $methodName = 'extend' . ucfirst($elementName);
        if (!method_exists($this, $methodName)) {
            $methodName = 'extendDefault';
        }
        $this->$methodName($target, $element, $overwrite);
    }

    private function extendDefault(\SimpleXMLElement $target, \SimpleXMLElement $element, $overwrite = false)
    {
        $elementName = $element->getName();

        if (!isset($target->$elementName)) {
            $this->createElement($target, $element, $overwrite);
        } else {
            $this->updateElement($target->$elementName, $element, $overwrite);
        }

        return $this;
    }

    private function createElement(\SimpleXMLElement  $target, \SimpleXMLElement $element, $overwrite)
    {
        $elementName = $element->getName();

        $newElement = $target->addChild($elementName, '');
        foreach ($element->attributes() as $key => $value) {
            $newElement->addAttribute($key, $this->xmlentities($value));
        }

        $value = (string) $element;
        if (strlen($value)) {
            $newElement[0] = $value;
        }

        if ($this->hasChildren($element)) {
            foreach ($element->children() as $k => $child) {
                $this->extendElement($newElement, $child, $overwrite);
            }
        }
    }

    private function updateElement(\SimpleXMLElement  $target, \SimpleXMLElement $element, $overwrite)
    {
        foreach ($element->attributes() as $key => $value) {
            $target[$key] = $this->xmlentities($value);
        }

        if ($this->hasChildren($element)) {
            foreach ($element->children() as $k => $child) {
                $this->extendElement($target, $child, $overwrite);
            }
        }
    }

    private function convert(\SimpleXMLElement $element)
    {
        if ($this->hasChildren($element)) {
            $result = new Data();
            foreach ($element->children() as $childName => $child) {
                $result->$childName = $this->convert($child);
            }
        } else {
            $result = (string) $element;
        }

        return $result;
    }

    /**
     * Converts meaningful xml characters to xml entities
     *
     * @param  string
     * @return string
     */
    private function xmlentities($value = null)
    {
        if (is_null($value)) {
            $value = $this;
        }
        $value = (string)$value;

        $value = str_replace(
            array('&', '"', "'", '<', '>'),
            array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'),
            $value
        );

        return $value;
    }

    private function hasChildren(\SimpleXMLElement $element)
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
