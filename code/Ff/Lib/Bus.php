<?php

namespace Ff\Lib;

use Ff\Lib\Configuration;
use Ff\Lib\Context;

use Ff\Runtime\Command;
use Ff\Runtime\Resource;
use Ff\Runtime\Service;
use Ff\Runtime\Render;
use Ff\Runtime\Ui;

class Bus
{
    /**
     *
     * @var Configuration
     */
    private $configurationInterface;

    /**
     * @var Context
     */
    private $context;

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
     * @var Render
     */
    private $renderInterface;

    /**
     *
     * @var Ui
     */
    private $uiInterface;

    /**
     *
     * @var array
     */
    private $instances = array();
    
    public function __construct(Configuration $configuration, Context $context)
    {
        $this->configurationInterface = $configuration;
        $this->context = $context;
    }
    
    public function configuration()
    {
        return $this->configurationInterface;
    }

    public function context()
    {
        return $this->context;
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

    public function render()
    {
        if (!isset($this->renderInterface)) {
            $this->renderInterface = new Render($this);
        }
        return $this->renderInterface;
    }

    public function ui()
    {
        if (!isset($this->uiInterface)) {
            $this->uiInterface = new Ui($this);
        }
        return $this->uiInterface;
    }

    public function getInstance($resourceIdentity)
    {
        if (!isset($this->instances[$resourceIdentity])) {
            $configuration = $this->configuration()->getResourceConfiguration($resourceIdentity);
            if (!$configuration || !$configuration->get('attributes/class')) {
                throw new \InvalidArgumentException("Wrong class name for the given resource: " . $resourceIdentity);
            }
            $class = $configuration->get('attributes/class');
            $resource = new $class($this, $configuration, $resourceIdentity);
            $this->instances[$resourceIdentity] = $resource;
        }

        return $this->instances[$resourceIdentity];
    }
}
