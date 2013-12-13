<?php

namespace Ff\Lib\Render\Html;

use Ff\Api\ConfigurationInterface;
use Ff\Lib\Bus;
use Ff\Lib\Data;
use Ff\Lib\Render\Html\Template\Processor;

class Template
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

    /**
     * @param Bus $bus
     * @param \SimpleXMLElement $element
     */
    public function __construct(Bus $bus, \SimpleXMLElement $element = null)
    {
        $this->bus = $bus;

        $this->templateProcessor = new Processor($bus);

        $this->root = $element;
    }

    /**
     * @param null $filePath
     */
    public function load($filePath = null)
    {
        if ($filePath === null) {
            $filePath = 'etc/configuration.xml';
        }

        $this->root = simplexml_load_file($filePath);
    }

    /**
     * @param \SimpleXMLElement $element
     * @param bool $overwrite
     * @return $this
     */
    public function extend(\SimpleXMLElement $element, $overwrite = false)
    {
        // extended elements should contain declaration starting from root
        $this->updateAttributes($this->root, $element, $overwrite);

        foreach ($element->children() as $child) {
            $this->extendElement($this->root, $child, $overwrite);
        }

        return $this;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getRoot()
    {
        return $this->root;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

    private function getAttribute(\SimpleXMLElement $element, $name)
    {
        $attributes = $element->attributes();
        return isset($attributes[$name]) ? (string)$attributes[$name] : null;
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

    private function updateElement(\SimpleXMLElement  $target, \SimpleXMLElement $element, $overwrite)
    {
        $this->updateAttributes($target, $element, $overwrite);

        if ($this->hasChildren($element)) {
            foreach ($element->children() as $k => $child) {
                $this->extendElement($target, $child, $overwrite);
            }
        }
    }

    private function updateAttributes(\SimpleXMLElement  $target, \SimpleXMLElement $element, $overwrite)
    {
        foreach ($element->attributes() as $key => $value) {
            $target[$key] = $value;
        }
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
