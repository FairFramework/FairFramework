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
     * @param \SimpleXMLElement $element
     * @param $prefix
     * @return string
     */
    public function process(\SimpleXMLElement $element, $prefix)
    {
        $result = '';

        $attributes = $element->attributes();
        if ($attributes) {
            if ($prefix) {
                $prefix .= '/';
            }
            foreach ($attributes as $key => $value) {
                $value = $this->processValue($value, $prefix);
                $result .= ' ' . $key . '="' . str_replace('"', '\"', $value) . '"';
            }
        }

        return $result;
    }

    public function processAssert($value, $prefix)
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

    public function processValue($value, $prefix = null)
    {
        $value = preg_replace_callback('#\{(.*?)\}#',
            function ($matches) use ($prefix) {
                return $this->resolveValue($matches[1], $prefix);
            }, $value);
        $value = preg_replace_callback('#\[(.*?)\]#',
            function ($matches) {
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
