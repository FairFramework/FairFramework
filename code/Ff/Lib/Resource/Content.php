<?php

namespace Ff\Lib\Resource;

use Ff\Lib\Resource;

class Content extends Resource
{
    protected $code = 'content';

    protected function load()
    {
        $element = $this->bus->configuration()->findElement($this->identity);
        $this->data = $this->bus->configuration()->toStructure($element, $this->identity);
    }
}
