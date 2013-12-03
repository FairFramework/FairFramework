<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Ui\AbstractElement;

class Title extends AbstractElement
{
    public function render($content, array $arguments = array())
    {
        return '<h1 ' . $this->getArgumentsHtml($arguments) . '>' . $content . '</h1>';
    }
}
