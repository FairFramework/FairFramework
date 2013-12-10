<?php

namespace Ff\Lib\Command;

use Ff\Api\ContextInterface;
use Ff\Lib\Bus;
use Ff\Runtime\Resource;

class View
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
     * @return \Ff\Lib\Resource
     */
    public function execute()
    {
        $context = $this->bus->context();

        $resourceIdentity = $context->getParam('resource_identity');

        $resource = $this->bus->getInstance($resourceIdentity);

        return $resource;
    }
}
