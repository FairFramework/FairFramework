<?php

namespace Ff\Lib\Ui;

use Ff\Api\Ui\ElementInterface;
use Ff;

class Element implements ElementInterface
{
    public function create(array $config)
    {
        $type = $config['ui_element_type'];
        $element = Ff::getUi()->getElement();
    }

    public function render()
    {
        //
    }
}
