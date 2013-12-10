<?php

namespace Ff\Lib\Resource;

use Ff\Lib\Data;
use Ff\Lib\Resource;

class Configuration extends Resource
{
    protected $code = 'configuration';

    protected function load()
    {
        if (strpos($this->identity, 'resource/configuration') === 0) {
            $identity = preg_replace('#resource/configuration#', '', $this->identity);
            $identity = ltrim($identity, '/');
        } else {
            $identity = $this->identity;
        }

        $element = $this->bus->configuration()->findElement($identity);
        $this->data = $this->bus->configuration()->toStructure($element, $this->identity);
    }
}
