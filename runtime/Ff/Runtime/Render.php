<?php

namespace Ff\Runtime;

use Ff\Lib\Bus;

class Render
{
    /**
     *
     * @var Bus
     */
    private $bus;

    /**
     * @var string
     */
    private $contentType;
    
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @return \Ff\Lib\Render\Html
     */
    public function html()
    {
        return $this->bus->getInstance("render/html");
    }
}
