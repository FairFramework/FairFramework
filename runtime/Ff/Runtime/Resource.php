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

    public function category()
    {
        return $this->bus->getInstance('resource/category');
    }

    public function product()
    {
        return $this->bus->getInstance('resource/product');
    }
    
    public function page()
    {
        return $this->bus->getInstance('resource/page');
    }
}
