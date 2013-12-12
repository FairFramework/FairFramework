<?php

namespace Ff\Lib\Render\Html\Template;

use Ff\Lib\Render\Html\Template;
use Ff\Lib\Bus;
use Ff\Lib\Data;

class Processor
{
    /**
     * @var \Ff\Lib\Bus
     */
    protected $bus;

    /**
     * @var Processor\Element
     */
    protected $elementProcessor;

    public function __construct(Bus $bus)
    {
        $this->bus = $bus;

        $this->elementProcessor = new Processor\Element($bus);
    }

    /**
     * @param \SimpleXMLElement $root
     * @return \SimpleXMLElement
     */
    public function prepare(\SimpleXMLElement $root)
    {
        return $this->elementProcessor->prepare($root);
    }

    /**
     * @param \SimpleXMLElement $element
     * @return string
     */
    public function render(\SimpleXMLElement $element)
    {
        return $this->elementProcessor->render($element);
    }
}
