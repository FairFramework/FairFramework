<?php

namespace Ff\Runtime;

use Ff\Lib\Bus;

class Service
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
    
    public function datastorage()
    {
        return $this->bus->getInstance('service/datastorage');
    }
}
