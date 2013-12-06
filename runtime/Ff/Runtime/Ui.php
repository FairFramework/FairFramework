<?php

namespace Ff\Runtime;

use Ff\Lib\Bus;

class Ui
{
    /**
     *
     * @var Bus
     */
    private $bus;
    
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @return \Ff\Lib\Ui\Title
     */
    public function title()
    {
        return $this->bus->getInstance("ui/title");
    }

    /**
     * @return \Ff\Lib\Ui\Text
     */
    public function text()
    {
        return $this->bus->getInstance("ui/text");
    }
}
