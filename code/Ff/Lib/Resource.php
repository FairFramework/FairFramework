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
     * @var null
     */
    protected $identity;

    /**
     * @param Bus $bus
     * @param Data $configuration
     * @param null $identity
     */
    public function __construct(Bus $bus, Data $configuration, $identity = null)
    {
        $this->bus = $bus;

        $this->config = $configuration;

        $this->identity = $identity;
        if ($this->identity) {
            $this->load();
        }
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

    protected function load()
    {
        $storage = $this->bus->service()->datastorage();
        $query = $storage->getQuery();
        $query->table($this->code);
        $query->fields('*');
        $query->where(array(
            $this->getIdentityName() => $this->identity
        ));

        $data = $storage->execute($query, 'fetch_row');
        if ($data === null) {
            $data = array();
        } else {
            $this->loadConfiguration();
        }
        $this->data = new Data($data);
    }

    protected function loadConfiguration()
    {
        $storage = $this->bus->service()->datastorage();
        $query = $storage->getQuery();
        $query->table($this->code . '_configuration');
        $query->fields('*');
        $query->where(array(
            $this->getIdentityName() => $this->identity
        ));

        $data = $storage->execute($query, 'fetch_all');
        if ($data === null) {
            $data = array();
        }
        $configuration = new Data($data);
        $this->config->extend($configuration);
    }

    private function getIdentityName()
    {
        return $this->code . '_id';
    }
}
