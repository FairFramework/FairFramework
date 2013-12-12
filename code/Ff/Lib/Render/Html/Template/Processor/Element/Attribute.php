<?php

namespace Ff\Lib\Render\Html\Template\Processor\Element;

use Ff\Api\ConfigurationInterface;
use Ff\Lib\Bus;
use Ff\Lib\Data;
use Ff\Lib\Render\Html\Template\Transport;

class Attribute
{
    /**
     * @var \Ff\Lib\Bus
     */
    protected $bus;

    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @param \SimpleXMLElement $resultElement
     * @param \SimpleXMLElement $sourceElement
     * @param $prefix
     * @return bool
     */
    public function prepare(\SimpleXMLElement $resultElement,\SimpleXMLElement $sourceElement, $prefix)
    {
        $attributes = $sourceElement->attributes();
        if ($attributes) {
            if ($prefix) {
                $prefix .= '/';
            }

            foreach ($attributes as $key => $value) {
                $method = 'prepareAttribute' . ucfirst($key);
                if (!method_exists($this, $method)) {
                    $method = 'prepareAttributeGeneric';
                }

                $value = $this->$method($key, $value, $prefix);

                $resultElement->addAttribute($key, $value);
            }
        }

        return true;
    }

    public function assert(\SimpleXMLElement $sourceElement, $prefix)
    {
        $value = isset($sourceElement['assert']) ? (string) $sourceElement['assert'] : '';
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

    public function prepareAttribute($name, $value, $prefix = null)
    {
        $method = 'prepareAttribute' . ucfirst($name);
        if (!method_exists($this, $method)) {
            $method = 'prepareAttributeGeneric';
        }

        return $this->$method($name, $value, $prefix);
    }

    /**
     * @param \SimpleXMLElement $element
     * @return string
     */
    public function render(\SimpleXMLElement $element)
    {
        $result = '';

        $attributes = $element->attributes();
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $result .= ' ' . $key . '="' . str_replace('"', '\"', $value) . '"';
            }
        }

        return $result;
    }

    private function prepareAttributeGeneric($key, $value, $prefix)
    {
        $value = preg_replace_callback('#\{(.*?)\}#',
            function ($matches) use ($key, $prefix) {
                return $this->resolveValue($matches[1], $prefix);
            }, $value);
        $value = preg_replace_callback('#\[(.*?)\]#',
            function ($matches) use ($key) {
                return $this->resolveValue($matches[1]);
            }
            , $value);

        return $value;
    }

    private function resolveValue($search, $prefix = '')
    {
        $search = $prefix . $search;
        $result = Transport::get($search);
        return $result;
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
}
