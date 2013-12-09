<?php

namespace Ff\Lib\Resource;

use Ff\Lib\Resource;

class Content extends Resource
{
    protected $code = 'content';

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
