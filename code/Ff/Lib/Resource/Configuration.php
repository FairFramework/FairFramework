<?php

namespace Ff\Lib\Resource;

use Ff\Lib\Data;
use Ff\Lib\Resource;

class Configuration extends Resource
{
    protected $code = 'configuration';

    public function load($identity)
    {
        if (strpos($identity, 'configuration') === 0) {
            $identity = str_replace('configuration', '', $identity);
            $identity = ltrim($identity, '/');
        }

        $element = $this->bus->configuration()->findElement($identity);
        $this->data = $this->bus->configuration()->toStructure($element);
    }
}
