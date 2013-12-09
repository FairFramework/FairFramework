<?php

namespace Ff\Lib;

use Ff\Lib\Bus;
use Ff\Runtime\Storage;

class Resource extends \stdClass
{
    protected $code = 'page';
    
    /**
     *
     * @var Bus
     */
    protected $bus;
    
    /**
     * @var Data
     */
    public $config;

    /**
     *
     * @var Data
     */
    public $data;

    /**
     * @param Bus $bus
     * @param Data $configuration
     */
    public function __construct(Bus $bus, Data $configuration)
    {
        $this->bus = $bus;

        $this->config = $configuration;
    }

    public function load($identity)
    {
        $storage = $this->bus->service()->datastorage();
        $query = $storage->getQuery();
        $query->table($this->code);
        $query->fields('*');
        $query->where(array(
            $this->getIdentityName() => $identity
        ));

        $data = $storage->execute($query, 'fetch_row');
        if ($data === null) {
            $data = array();
        } else {
            $this->loadConfiguration($identity);
        }
        $this->data = new Data($data);
    }

    private function loadConfiguration($identity)
    {
        $storage = $this->bus->service()->datastorage();
        $query = $storage->getQuery();
        $query->table($this->code . '_configuration');
        $query->fields('*');
        $query->where(array(
            $this->getIdentityName() => $identity
        ));

        $data = $storage->execute($query, 'fetch_all');
        if ($data === null) {
            $data = array();
        }
        $configuration = new Data($data);
        $this->config->extend($configuration);
    }

    public function getData($key = null)
    {
        if (null === $key) {
            return $this->data;
        }
        return $this->data->get($key);
    }

    public function getConfig($key = null)
    {
        if (null === $key) {
            return $this->config;
        }
        return $this->config->get($key);
    }
    
    public function getCode()
    {
        return $this->code;
    }

    private function getIdentityName()
    {
        return $this->code . '_id';
    }
}
