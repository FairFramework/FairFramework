<?php

namespace Ff\Lib\Command;

use Ff\Lib\Bus;

class Create
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
     * @return string
     */
    public function execute()
    {
        //
        return '';
    }
}
