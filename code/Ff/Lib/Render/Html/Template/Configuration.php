<?php

namespace Ff\Lib\Render\Html\Template;

use Ff\Api\ConfigurationInterface;
use Ff\Lib\Bus;
use Ff\Lib\Data;

class Configuration implements ConfigurationInterface
{
    /**
     * @var \Ff\Lib\Ui\AbstractElement[]
     */
    private static $renders = array();

    /**
     * @var \SimpleXMLElement
     */
    protected $config;

    /**
     * @var \Ff\Lib\Bus
     */
    protected $bus;

    protected $singleTagElements = array(
        'link', 'hr', 'meta', 'br'
    );

    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
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

    private function replaceWithData($search, $data, $attribute)
    {
        $search = (string) $search;
        $result = $data->get($search);
        if ($result) {
            if (in_array($attribute, array('if', 'nif'))) {
                return true;
            }
            return $result;
        } else {
            return null;
        }
    }

    private function assertCondition($search, $compare, $data)
    {
        $search = (string) $search;
        $result = $data->get($search);
        if ($result === $compare) {
            return 'CONTINUE';
        } elseif (!$result && !$compare) {
            return 'CONTINUE';
        } else {
            return 'SKIP';
        }
    }

    /**
     * @param \SimpleXMLElement $element
     * @param int $level
     * @param Data $data
     * @param Data $globalData
     * @return string
     */
    public function toHtml(\SimpleXMLElement $element = null, $level = 0, Data $data, Data $globalData)
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
        $parsedAttributes = array();
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                if ($key === 'assert') {
                    $value = preg_replace_callback('#\$\((.*?)\)=\((.*?)\)#',
                        function ($matches) use ($globalData, $key) {
                            return $this->assertCondition($matches[1], $matches[2], $globalData);
                        }, $value);
                    $value = preg_replace_callback('#\@\((.*?)\)=\((.*?)\)#',
                        function ($matches) use ($data, $key) {
                            return $this->assertCondition($matches[1], $matches[2], $data);
                        }
                        , $value);
                    if ($value === 'SKIP') {
                        return '';
                    }
                    continue;
                }

                $value = preg_replace_callback('#\$\((.*?)\)#',
                    function ($matches) use ($globalData, $key) {
                        return $this->replaceWithData($matches[1], $globalData, $key);
                    }, $value);
                $value = preg_replace_callback('#\@\((.*?)\)#',
                    function ($matches) use ($data, $key) {
                        return $this->replaceWithData($matches[1], $data, $key);
                    }
                    , $value);

                if ($key === 'if') {
                    if (empty($value)) {
                        return '';
                    }
                    continue;
                }
                if ($key === 'nif') {
                    if (!empty($value)) {
                        return '';
                    }
                    continue;
                }

                $out .= ' ' . $key . '="' . str_replace('"', '\"', $value) . '"';
                $parsedAttributes[$key] = $value;
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
            $out .= ' />' . $nl;
            return $out;
        }

        $out .= '>';

        $dataAttributes = new Data($parsedAttributes);

        if ($dataAttributes->uiType) {
            $uiTypeRender = $this->getUiTypeRender($dataAttributes->uiType);
            return $uiTypeRender->render($data, $dataAttributes, $globalData);
        }

        if ($dataAttributes->dataCollection) {
            $collection = $data->get($dataAttributes->dataCollection);
        } else {
            $collection = array($data);
        }

        if ($collection) {
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
        }

        $out .= '</' . $elementName . '>' . $nl;

        return $out;
    }

    private function getUiTypeRender($uiType)
    {
        if (!isset(self::$renders[$uiType])) {
            $uiType = str_replace('_', '/', $uiType);
            self::$renders[$uiType] = $this->bus->getInstance('ui/' . $uiType);
        }

        return self::$renders[$uiType];
    }
}
