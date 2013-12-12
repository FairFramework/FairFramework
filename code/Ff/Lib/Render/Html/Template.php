<?php

namespace Ff\Lib\Render\Html;

use Ff\Api\ConfigurationInterface;
use Ff\Lib\Bus;
use Ff\Lib\Data;
use Ff\Lib\Render\Html\Template\Processor;

class Template implements ConfigurationInterface
{
    /**
     * @var \Ff\Lib\Ui\AbstractElement[]
     */
    private static $renders = array();

    /**
     * @var \SimpleXMLElement
     */
    protected $root;

    /**
     * @var \Ff\Lib\Bus
     */
    protected $bus;

    /**
     * @var Template\Processor
     */
    protected $templateProcessor;

    protected $singleTagElements = array(
        'link', 'hr', 'meta', 'br'
    );

    public function __construct(Bus $bus, \SimpleXMLElement $element = null)
    {
        $this->bus = $bus;

        $this->templateProcessor = new Processor($bus);

        $this->root = $element;
    }

    public function load($filePath = null)
    {
        if ($filePath === null) {
            $filePath = 'etc/configuration.xml';
        }

        $this->root = simplexml_load_file($filePath);
    }

    public function extend(\SimpleXMLElement $element, $overwrite = false)
    {
        $this->updateAttributes($this->root, $element, $overwrite);

        foreach ($element->children() as $child) {
            $this->extendElement($this->root, $child, $overwrite);
        }

        return $this;
    }

    public function prepare($localRefPrefix = null)
    {
        return $this->templateProcessor->prepare($this->root, $localRefPrefix);
    }

    public function render()
    {
        $element = $this->prepare();
        return $this->templateProcessor->render($element);
    }

    public function getRoot()
    {
        return $this->root;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
            return $this->root;
        }
        $pathArr = explode('/', $path);

        $element = $this->root;
        foreach ($pathArr as $nodeName) {
            $element = $element->$nodeName;
            if (!$element) {
                return false;
            }
        }
        return $element;
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
        $this->updateAttributes($newElement, $element, $overwrite);

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

    private function updateAttributes(\SimpleXMLElement  $target, \SimpleXMLElement $element, $overwrite)
    {
        foreach ($element->attributes() as $key => $value) {
            $target[$key] = $this->xmlentities($value);
        }
    }

    private function updateElement(\SimpleXMLElement  $target, \SimpleXMLElement $element, $overwrite)
    {
        $this->updateAttributes($target, $element, $overwrite);

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

    private function getUiTypeRender($uiType)
    {
        if (!isset(self::$renders[$uiType])) {
            $uiType = str_replace('_', '/', $uiType);
            self::$renders[$uiType] = $this->bus->getInstance('ui/' . $uiType);
        }

        return self::$renders[$uiType];
    }
}
