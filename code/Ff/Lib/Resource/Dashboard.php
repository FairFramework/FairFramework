<?php

namespace Ff\Lib\Resource;

use Ff\Lib\Data;
use Ff\Lib\Resource;

class Dashboard extends Resource
{
    protected $code = 'dashboard';

    protected function load()
    {
        $this->data = new Data();
    }
}
