<?php

namespace Ff\Lib\Ui;

abstract class AbstractElement
{
    abstract public function render($content, array $arguments = array());

    protected function getArgumentsHtml(array $arguments)
    {
        $html = array();
        foreach ($arguments as $name => $value) {
            $html[] = $name . '=' . '"' . $value . '"';
        }
        return implode(' ', $html);
    }
}
