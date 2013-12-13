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
     * @param \SimpleXMLElement $element
     * @param int $level
     * @param null $prefix
     * @return string
     */
    public function process(\SimpleXMLElement $element, $level = 0, $prefix = null)
    {
        $tag = $element->getName();

        if ($this->getAttribute($element, 'data')) {
            $value = $this->getAttribute($element, 'data');
            list($prefix, $uri) = explode(':', $value);
            if (!Transport::get($prefix)) {
                $uri = $this->attributeProcessor->processValue($uri);
                $resource = $this->bus->getInstance($uri);
                if ($resource) {
                    Transport::set($prefix, $resource->getData());
                }
            }
        }

        $assert = $this->getAttribute($element, 'assert');
        $result = $this->attributeProcessor->processAssert($assert, $prefix);
        if ($result === false) {
            return false;
        }

        if ($tag == 'ui') {
            $uiTypeRender = $this->getUiTypeRender($this->getAttribute($element, 'type'));
            $element = $uiTypeRender->prepare($element, $prefix);
            $tag = $element->getName();
        }

        if (is_numeric($level)) {
            $pad = str_pad('', $level*3, ' ', STR_PAD_LEFT);
            $nl = "\n";
        } else {
            $pad = '';
            $nl = '';
        }

        $level++;

        $result = $nl . $pad . '<' . $tag;

        $result .= $this->attributeProcessor->process($element, $prefix);

        if (in_array($tag, $this->singleTagElements)) {
            $result .= ' />';
            return $result;
        }

        $result .= '>';

        if ($this->hasChildren($element)) {
            if ($collectionUri = $this->getAttribute($element, 'collection')) {
                if ($prefix) {
                    $collectionUri = $prefix . '/' . $collectionUri;
                }
                $collection = Transport::get($collectionUri);
                if ($collection) {
                    foreach ($collection as $id => $item) {
                        $itemPrefix = $collectionUri . '/' . $id;
                        foreach ($element->children() as $child) {
                            $result .= $this->process($child, $level, $itemPrefix);
                        }
                    }
                }
            } else {
                foreach ($element->children() as $child) {
                    $result .= $pad . $this->process($child, $level, $prefix);
                }
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
            if ($prefix) {
                $prefix .= '/';
            }
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
