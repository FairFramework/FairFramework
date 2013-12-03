<?php

namespace Ff\Lib;

use Ff\Api\WidgetInterface;
use Ff\Api\RequestInterface;

abstract class Widget implements WidgetInterface
{
    /**
     * @var \Ff\Api\RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var array
     */
    protected $data = array();

    public function __construct(RequestInterface $request, array $config = array(), array $data = array())
    {
        $this->request = $request;

        $this->config = $config;
        $this->data = $data;
    }

    public function setConfig(array $config = array())
    {
        $this->config = $config;
    }

    public function setData(array $data = array())
    {
        $this->data = $data;
    }


    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

    public function getData($key = null)
    {
        if ($key === null) {
            return $this->data;
        }

        return isset($this->data[$key]) ? $this->data[$key] : null;
    }


    abstract public function load();

    abstract public function render();
}
