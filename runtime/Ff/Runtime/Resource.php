<?php

namespace Ff\Runtime;

use Ff\Lib\Bus;

class Resource
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

    public function dashboard()
    {
        return $this->bus->getInstance('resource/dashboard');
    }

    public function configuration()
    {
        return $this->bus->getInstance('resource/configuration');
    }

    public function content()
    {
        return $this->bus->getInstance('resource/content');
    }
}
