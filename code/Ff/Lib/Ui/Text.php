<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Ui\AbstractElement;

class Text extends AbstractElement
{
    public function render($content, array $arguments = array())
    {
        return '<p ' . $this->getArgumentsHtml($arguments) . '>' . $content . '</p>';
    }
}
