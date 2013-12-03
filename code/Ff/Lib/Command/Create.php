<?php

namespace Ff\Lib\Command;

use Ff\Api\ContextInterface;
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
     * @param ContextInterface $context
     * @return string
     */
    public function execute(ContextInterface $context)
    {
        //
        return '';
    }
}
