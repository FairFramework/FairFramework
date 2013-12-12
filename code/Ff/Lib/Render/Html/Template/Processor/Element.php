<?php

namespace Ff\Lib\Render\Html\Template\Processor;

use Ff\Lib\Bus;
use Ff\Lib\Data;
use Ff\Lib\Render\Html\Template\Transport;

class Element
{
    /**
     * @var \Ff\Lib\Bus
     */
    protected $bus;

    /**
     * @var \Ff\Lib\Render\Html\Template\Processor\Element\Attribute
     */
    protected $attributeProcessor;

    /**
     * @var \Ff\Lib\Ui\AbstractElement[]
     */
    private static $renders = array();

    protected $singleTagElements = array(
        'link', 'hr', 'meta', 'br'
    );

    public function __construct(Bus $bus)
    {
        $this->bus = $bus;

        $this->attributeProcessor = new Element\Attribute($bus);
    }

    /**
     * @param \SimpleXMLElement $sourceElement
     * @param \SimpleXMLElement $parent
     * @param null $localRefPrefix
     * @return bool|\SimpleXMLElement
     */
    public function prepare(\SimpleXMLElement $sourceElement, \SimpleXMLElement $parent = null, $localRefPrefix = null)
    {
        $tag = $sourceElement->getName();
        if ($tag === 'data') {
            $resourceName = (string)$sourceElement;
            $resource = $this->bus->getInstance($resourceName);
            if ($resource) {
                Transport::set($this->getAttribute($sourceElement, 'name'), $resource->getData());
            }
            return false;
        }

        if ($this->getAttribute($sourceElement, 'local_reference_prefix')) {
            $value = $this->getAttribute($sourceElement, 'local_reference_prefix');
            $localRefPrefix = $this->attributeProcessor->prepareAttribute('local_reference_prefix', $value);
            $resource = $this->bus->getInstance($localRefPrefix);
            if ($resource) {
                Transport::set($localRefPrefix, $resource->getData());
            }
        }

        $result = $this->attributeProcessor->assert($sourceElement, $localRefPrefix);
        if ($result === false) {
            return false;
        }
        unset($sourceElement['assert']);

        if ($tag == 'ui') {
            $uiTypeRender = $this->getUiTypeRender($this->getAttribute($sourceElement, 'type'));
            $sourceElement = $uiTypeRender->prepare($sourceElement, $localRefPrefix);
        }

        if (isset($parent)) {
            $resultElement = $parent->addChild($sourceElement->getName(), '');
        } else {
            $xml = "<{$sourceElement->getName()}></{$sourceElement->getName()}>";
            $resultElement = new \SimpleXMLElement($xml);
        }

        $resultElement[0] = (string)$sourceElement;

        $this->attributeProcessor->prepare($resultElement, $sourceElement, $localRefPrefix);

        if ($localRefPrefix) {
            $resultElement['local_reference_prefix'] = $localRefPrefix;
        }

        if ($this->getAttribute($sourceElement, 'collection')) {
            $collectionUri = $this->getAttribute($sourceElement, 'collection');
            if ($localRefPrefix) {
                $collectionUri = $localRefPrefix . '/' . $collectionUri;
            }
            $collection = Transport::get($collectionUri);
            if ($collection) {
                foreach ($collection as $id => $item) {
                    if ($this->hasChildren($sourceElement)) {
                        $_localRefPrefix = $collectionUri . '/' . $id;
                        foreach ($sourceElement->children() as $child) {
                            $this->prepare($child, $resultElement, $_localRefPrefix);
                        }
                    }
                }
            }
        } else {
            if ($this->hasChildren($sourceElement)) {
                foreach ($sourceElement->children() as $child) {
                    $this->prepare($child, $resultElement, $localRefPrefix);
                }
            }
        }

        return $resultElement;
    }

    /**
     * @param \SimpleXMLElement $element
     * @param int $level
     * @param null $prefix
     * @return string
     */
    public function render(\SimpleXMLElement $element, $level = 0, $prefix = null)
    {
        if (is_numeric($level)) {
            $pad = str_pad('', $level*3, ' ', STR_PAD_LEFT);
            $nl = "\n";
        } else {
            $pad = '';
            $nl = '';
        }

        $level++;

        $prefix = ((string) $this->getAttribute($element, 'local_reference_prefix'))
            ? ((string) $this->getAttribute($element, 'local_reference_prefix') . '/')
            : $prefix;

        $tag = $element->getName();

        $result = $nl . $pad . '<' . $tag;

        $result .= $this->attributeProcessor->render($element);

        if (in_array($tag, $this->singleTagElements)) {
            $result .= ' />';
            return $result;
        }

        $result .= '>';

        if ($this->hasChildren($element)) {
            foreach ($element->children() as $child) {
                $result .= $pad . $this->render($child, $level, $prefix);
            }
        }

        $result .= $this->processTextValue($element, $prefix);

        $result .= $nl . $pad . '</' . $tag . '>';

        return $result;
    }

    protected function getAttribute(\SimpleXMLElement $element, $name, $ns = null)
    {
        if ($ns) {
            $attributes = $element->attributes($ns, true);
        } else {
            $attributes = $element->attributes();
        }

        return isset($attributes[$name]) ? (string)$attributes[$name] : null;
    }

    protected function hasChildren(\SimpleXMLElement $element)
    {
        if (!$element->children()) {
            return false;
        }

        foreach ($element->children() as $child) {
            return true;
        }

        return false;
    }

    protected function getUiTypeRender($uiType)
    {
        if (!isset(self::$renders[$uiType])) {
            $uiType = str_replace('_', '/', $uiType);
            self::$renders[$uiType] = $this->bus->getInstance('ui/' . $uiType);
        }

        return self::$renders[$uiType];
    }

    protected function processTextValue(\SimpleXMLElement $element, $prefix)
    {
        $value = trim((string)$element);
        if (strlen($value)) {
            $localReplace = function ($matches) use($prefix) {
                return $this->replaceWithData($matches[1], $prefix);
            };

            $value = preg_replace_callback('#\{(.*?)\}#', $localReplace, $value);

            $globalReplace = function ($matches) use($prefix) {
                return $this->replaceWithData($matches[1], $prefix);
            };

            $value = preg_replace_callback('#\[(.*?)\]#', $globalReplace, $value);

            $value = $this->xmlEntities($value);
        }

        return $value;
    }

    protected function replaceWithData($search, $prefix)
    {
        $search = $prefix . $search;
        return Transport::get($search);
    }

    /**
     * Converts meaningful xml characters to xml entities
     *
     * @param  string
     * @return string
     */
    protected function xmlEntities($value = null)
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
}
