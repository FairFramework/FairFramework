<?php

namespace Ff\Lib\Ui\Menu;

use Ff\Lib\Data;
use Ff\Lib\Ui\AbstractElement;

class SubItem extends AbstractElement
{
    public function render(\SimpleXMLElement $element, Data $data, Data $globalData)
    {
        $result = '<ul class="dropdown-menu" role="menu">';
        $result .= '<li ' . $this->getAttributesHtml($element) . '>' . $data . '</li>';
        $result .= '</ul>';

        return $result;
    }
}
