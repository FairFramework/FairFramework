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
     * @var \Ff\Lib\Ui\AbstractElement[]
     */
    private static $renders = array();

    protected $singleTagElements = array(
        'link', 'hr', 'meta', 'br'
    );

    /**
     * @param Bus $bus
     */
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
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
            $uri = $this->processTextValue($uri);
            $resource = $this->bus->getInstance($uri);
            if ($resource) {
                Transport::set($prefix, $resource->getData());
            }
        }

        $assert = $this->getAttribute($element, 'assert');
        $result = $this->processAssert($assert, $prefix);
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

        $result .= $this->processAttributes($element, $prefix);

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

        $value = trim((string)$element);
        $result .= $this->processTextValue($value, $prefix);

        $result .= $nl . $pad . '</' . $tag . '>';

        return $result;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function getAttribute(\SimpleXMLElement $element, $name, $ns = null)
    {
        if ($ns) {
            $attributes = $element->attributes($ns, true);
        } else {
            $attributes = $element->attributes();
        }

        return isset($attributes[$name]) ? (string)$attributes[$name] : null;
    }

    private function hasChildren(\SimpleXMLElement $element)
    {
        if (!$element->children()) {
            return false;
        }

        foreach ($element->children() as $child) {
            return true;
        }

        return false;
    }

    private function getUiTypeRender($uiType)
    {
        if (!isset(self::$renders[$uiType])) {
            $uiType = str_replace('_', '/', $uiType);
            self::$renders[$uiType] = $this->bus->getInstance('ui/' . $uiType);
        }

        return self::$renders[$uiType];
    }

    private function processAssert($value, $prefix)
    {
        if ($value) {
            if ($prefix) {
                $prefix .= '/';
            }
            $value = preg_replace_callback('#(!?)\{([/a-z]*)\}(=?)([a-z0-9]*)#',
                function ($matches) use ($prefix) {
                    return $this->assertCondition($matches[2], $matches[3], $matches[4], $matches[1], $prefix);
                }
                , $value);

            $value = preg_replace_callback('#(\!?)\[([/a-z]*+)\](=?)([a-z0-9]+$)#',
                function ($matches) {
                    return $this->assertCondition($matches[2], $matches[3], $matches[4], $matches[1]);
                }, $value);

            if ($value === '__SKIP__') {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    private function assertCondition($var, $operand, $compare, $denial, $prefix = '')
    {
        $result = false;
        $value = Transport::get($prefix . $var);
        if ($operand) {
            switch ($operand) {
                case '=':
                    if ($value === $compare) {
                        $result = true;
                    }
                    break;
                case '>':
                    if ($value > $compare) {
                        $result = true;
                    }
                    break;
                case '<':
                    if ($value < $compare) {
                        $result = true;
                    }
                    break;
            }
        }  else {
            $result = !empty($value);
        }

        if ($denial) {
            $result = !$result;
        }

        if ($result) {
            return '__CONTINUE__';
        } else {
            return '__SKIP__';
        }
    }

    private function processTextValue($value, $prefix = null)
    {
        if (strlen($value)) {
            if ($prefix) {
                $prefix .= '/';
            }
            $localReplace = function ($matches) use($prefix) {
                $search = $prefix . $matches[1];
                return Transport::get($search);
            };

            $value = preg_replace_callback('#\{(.*?)\}#', $localReplace, $value);

            $globalReplace = function ($matches) {
                return Transport::get($matches[1]);
            };

            $value = preg_replace_callback('#\[(.*?)\]#', $globalReplace, $value);

            $value = $this->xmlEntities($value);
        }

        return $value;
    }

    private function xmlEntities($value)
    {
        $value = str_replace(
            array('&', '"', "'", '<', '>'),
            array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'),
            $value
        );

        return $value;
    }

    private function processAttributes(\SimpleXMLElement $element, $prefix)
    {
        $result = '';

        $attributes = $element->attributes();
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $value = $this->processTextValue($value, $prefix);
                $result .= ' ' . $key . '="' . str_replace('"', '\"', $value) . '"';
            }
        }

        return $result;
    }
}
