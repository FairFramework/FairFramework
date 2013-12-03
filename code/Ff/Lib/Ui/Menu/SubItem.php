<?php

namespace Ff\Lib\Ui\Menu;

use Ff\Lib\Ui\AbstractElement;

class SubItem extends AbstractElement
{
    public function render($content, array $arguments = array())
    {
        $result = '<ul class="dropdown-menu" role="menu">';
        $result .= '<li ' . $this->getArgumentsHtml($arguments) . '>' . $content . '</li>';
        $result .= '</ul>';

        return $result;
    }
}
