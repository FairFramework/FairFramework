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
    private $bus;
    
    /**
     * @var Data
     */
    public $config;

    /**
     *
     * @var Data
     */
    public $data;
    
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;

        $config = $this->bus->configuration()->get('resource/page');
        $this->config = new Data($config);
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
        
        $this->data = new Data($data);
    }

    public function getData($key = null)
    {
        if (null === $key) {
            return $this->data;
        }
        return $this->data->getData($key);
    }

    public function getConfig($key = null)
    {
        if (null === $key) {
            return $this->config;
        }
        return $this->data->getData($key);
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
