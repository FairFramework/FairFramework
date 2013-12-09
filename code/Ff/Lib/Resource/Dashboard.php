<?php

namespace Ff\Lib\Resource;

use Ff\Lib\Data;
use Ff\Lib\Resource;

class Dashboard extends Resource
{
    protected $code = 'dashboard';

    public function load($identity)
    {
        $this->data = new Data();
    }
}
