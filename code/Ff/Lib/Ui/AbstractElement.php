<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Bus;
use Ff\Lib\Data;

abstract class AbstractElement
{
    /**
     *
     * @var Bus
     */
    protected $bus;

    /**
     * @var Data
     */
    public $config;

    /**
     * @param Bus $bus
     * @param Data $configuration
     */
    public function __construct(Bus $bus, Data $configuration)
    {
        $this->bus = $bus;

        $this->config = $configuration;
    }

    abstract public function render(\SimpleXMLElement $element, Data $data, Data $globalData);

    protected function getAttributesHtml(\SimpleXMLElement $element)
    {
        $attributes = (array) $element->attributes();
        $html = array();
        foreach ($attributes as $name => $value) {
            $html[] = $name . '=' . '"' . $value . '"';
        }
        return implode(' ', $html);
    }

    protected function getAttribute(\SimpleXMLElement $element, $name)
    {
        $attributes = $element->attributes();
        return isset($attributes[$name]) ? (string)$attributes[$name] : null;
    }
}
