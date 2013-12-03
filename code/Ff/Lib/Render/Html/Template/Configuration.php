<?php

namespace Ff\Lib\Render\Html\Template;

use Ff\Api\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var \SimpleXMLElement
     */
    protected $config;

    protected $singleTagElements = array(
        'link', 'hr', 'meta'
    );

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

    private function extendScript(\SimpleXMLElement $target, \SimpleXMLElement $element, $overwrite = false)
    {
        $this->createElement($target, $element, $overwrite);

        return $this;
    }

    private function extendLink(\SimpleXMLElement $target, \SimpleXMLElement $element, $overwrite = false)
    {
        $this->createElement($target, $element, $overwrite);

        return $this;
    }

    private function extendHead(\SimpleXMLElement $target, \SimpleXMLElement $element, $overwrite = false)
    {
        $elementName = $element->getName();

        if (!isset($target->$elementName)) {
            $this->createElement($target, $element, $overwrite);
        } else {
            $this->updateElement($target->$elementName, $element, $overwrite);
        }

        return $this;
    }

    private function extendBody(\SimpleXMLElement $target, \SimpleXMLElement $element, $overwrite = false)
    {
        $elementName = $element->getName();

        if (!isset($target->$elementName)) {
            $this->createElement($target, $element, $overwrite);
        } else {
            $this->updateElement($target->$elementName, $element, $overwrite);
        }

        return $this;
    }

    private function extendDefault(\SimpleXMLElement $target, \SimpleXMLElement $element, $overwrite = false)
    {
        $elementName = $element->getName();

        if (!isset($target->$elementName)) {
            $this->createElement($target, $element, $overwrite);
        } else {
            $identifier = $this->getAttribute($element, 'id');
            $targetElement = $target->xpath($elementName . "[@id='$identifier']");
            if (!$targetElement) {
                $this->createElement($target, $element, $overwrite);
            } else {
                $this->updateElement($targetElement[0], $element, $overwrite);
            }
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

    public function getAttribute(\SimpleXMLElement $element, $name)
    {
        $attributes = $element->attributes();
        return isset($attributes[$name]) ? (string)$attributes[$name] : null;
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

    private function replaceWithData($search, $data, $default)
    {
        $search = (string) $search;
        $try = $data->get($search);
        if ($try) {
            return $try;
        } else {
            return (string) $default;
        }
    }

    /**
     * @param \SimpleXMLElement $element
     * @param int $level
     * @param \stdClass $data
     * @param \stdClass $globalData
     * @return string
     */
    public function toHtml(\SimpleXMLElement $element = null, $level = 0, \stdClass $data, \stdClass $globalData)
    {
        if ($element === null) {
            $element = $this->config;
        }

        $replaceGlobal = function ($matches) use ($globalData) {
            return $this->replaceWithData($matches[1], $globalData, '');
        };

        $elementName = $element->getName();

        if (is_numeric($level)) {
            $pad = str_pad('', $level*3, ' ', STR_PAD_LEFT);
            $nl = "\n";
        } else {
            $pad = '';
            $nl = '';
        }

        $out = $pad . '<' . $elementName;

        $attributes = $element->attributes();
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $value = preg_replace_callback('#\$\((.*?)\)#',
                    function ($matches) use ($globalData) {
                        return $this->replaceWithData($matches[1], $globalData, '');
                    }, $value);

                $value = preg_replace_callback('#\@\((.*?)\)#',
                    function ($matches) use ($data) {
                        return $this->replaceWithData($matches[1], $data, '');
                    }
                    , $value);
                $out .= ' ' . $key . '="' . str_replace('"', '\"', $value) . '"';

                if ($key === 'if') {
                    if (empty($value)) {
                        return '';
                    }
                }
                if ($key === 'nif') {
                    if (!empty($value)) {
                        return '';
                    }
                }
            }
        }

        $xsiAttributes = $element->attributes('xsi', true);
        if ($xsiAttributes) {
            foreach ($xsiAttributes as $key => $value) {
                $value = preg_replace_callback('#\$\((.*?)\)#', $replaceGlobal, $value);
                $out .= ' xsi:' . $key . '="' . str_replace('"', '\"', $value) . '"';
            }
        }

        if (in_array($elementName, $this->singleTagElements)) {
            $out .= '/>' . $nl;
            return $out;
        }

        $out .= '>';

        if ($this->getAttribute($element, 'data-collection')) {
            $name = (string)$this->getAttribute($element, 'data-collection');
            $collection = $data->get($name);
        }
        if (!isset($collection)) {
            $collection = array($data);
        }

        foreach ($collection as $item) {
            if ($this->hasChildren($element)) {
                $value = trim((string)$element);
                if (strlen($value)) {
                    $out .= $this->xmlentities($value);
                }
                $out .= $nl;
                foreach ($element->children() as $child) {
                    $out .= $this->toHtml($child, $level+1, $item, $globalData);
                }
                $out .= $pad;
            }
            $value = (string)$element;
            if (strlen($value)) {
                $replace = function ($matches) use ($item) {
                    return $this->replaceWithData($matches[1], $item, '');
                };

                $value = preg_replace_callback('#\@\((.*?)\)#', $replace, $value);
                $value = preg_replace_callback('#\$\((.*?)\)#', $replaceGlobal, $value);

                $out .= $this->xmlentities($value);
            }
        }

        $out .= '</' . $elementName . '>' . $nl;

        return $out;
    }
}
