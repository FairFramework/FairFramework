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
    private $bus;

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

    abstract public function render($content, array $arguments = array());

    protected function getArgumentsHtml(array $arguments)
    {
        $html = array();
        foreach ($arguments as $name => $value) {
            $html[] = $name . '=' . '"' . $value . '"';
        }
        return implode(' ', $html);
    }
}
