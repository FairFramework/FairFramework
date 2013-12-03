<?php

namespace Ff\Lib\Ui;

use Ff\Api\Ui\PageInterface;
use Ff\Api\RequestInterface;

use Ff;

abstract class Page implements PageInterface
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

    protected $elements = array();

    protected $layout;

    public function __construct(RequestInterface $request, array $config = array(), array $data = array())
    {
        $this->request = $request;

        $this->config = $config;
        $this->data = $data;
    }

    public function addElement($name, array $config)
    {
        $this->elements[$name] = $config;
    }

    public function render()
    {
        $this->prepareElements();
    }

    protected function prepareElements()
    {
        if (!empty($this->config['elements'])) {
            foreach ($this->config['elements'] as $name => $element) {
                $this->elements[$name] = $this->prepareElement($element);
            }
        }
    }

    protected function prepareElement($element)
    {
        return Ff::getSpi()->getUiElement()->create($element);
    }
}
