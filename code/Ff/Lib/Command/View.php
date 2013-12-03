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
     * @param ContextInterface $context
     * @return \Ff\Lib\Resource
     */
    public function execute(ContextInterface $context)
    {
        $resourceName = $context->getParam('resource_name', 'page');
        $resourceIdentity = $context->getParam('resource_identity', 'home');

        $resource = $this->bus->resource()->$resourceName();
        $resource->load($resourceIdentity);

        return $resource;
    }
}
