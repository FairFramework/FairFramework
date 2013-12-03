<?php

namespace Ff\Lib;

use Ff\Lib\Configuration;
use Ff\Runtime\Command;
use Ff\Runtime\Resource;
use Ff\Runtime\Service;

class Bus
{
    /**
     *
     * @var Configuration
     */
    private $configurationInterface;

    /**
     *
     * @var Command
     */
    private $commandInterface;
    
    /**
     *
     * @var Resource
     */
    private $resourceInterface;
    
    /**
     *
     * @var Service
     */
    private $serviceInterface;
    
    /**
     *
     * @var array
     */
    private $instances = array();
    
    public function __construct(Configuration $configuration)
    {
        $this->configurationInterface = $configuration;
    }
    
    public function configuration()
    {
        return $this->configurationInterface;
    }
    
    public function command()
    {
        if (!isset($this->commandInterface)) {
            $this->commandInterface = new Command($this);
        }
        return $this->commandInterface;
    }

    public function resource()
    {
        if (!isset($this->resourceInterface)) {
            $this->resourceInterface = new Resource($this);
        }
        return $this->resourceInterface;
    }

    public function service()
    {
        if (!isset($this->serviceInterface)) {
            $this->serviceInterface = new Service($this);
        }
        return $this->serviceInterface;
    }
    
    public function getInstance($identifier)
    {
        if (!isset($this->instances[$identifier])) {
            $className = $this->resolveClassName($identifier);
            $this->instances[$identifier] = new $className($this);
        }
        
        return $this->instances[$identifier];
    }

    ////////////////////////////////////////////////////////////////////////////
    
    private function resolveClassName($identifier)
    {
        $className = $this->configurationInterface->get("{$identifier}/class");
        
        return $className;
    }
}
