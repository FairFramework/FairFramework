<?php

namespace Ff\Api;

interface WidgetInterface
{
    public function setConfig(array $config = array());

    public function setData(array $data = array());


    public function getConfig($key = null);

    public function getData($key = null);


    public function load();

    public function render();
}
