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

    /**
     * @param Bus $bus
     */
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;

        $this->elementProcessor = new Processor\Element($bus);
    }

    /**
     * @param Template $template
     * @return string
     */
    public function process(Template $template)
    {
        $root = $template->getRoot();
        return $this->elementProcessor->process($root);
    }
}
