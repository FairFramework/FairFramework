<?php

namespace Ff\Api\Ui;

use Ff\Api\RequestInterface;

interface PageInterface
{
    public function __construct(RequestInterface $request, array $config = array(), array $data = array());

    public function addElement($name, array $configuration);

    public function load();

    public function render();
}
