<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Ui\AbstractElement;

class Grid extends AbstractElement
{
    public function render($content, array $arguments = array())
    {
        $result = '<table>';
        foreach ($content as $rowId => $item) {
            $result .= '<tr item-id="' . $rowId . '">';
            foreach ($item->fields as $fieldName => $value) {
                $result .= '<td item-name="' . $fieldName . '">' . $value->value . '</td>';
            }
            $result .= '</tr>';
        }
        $result .= '</table>';
        return $result;
    }
}
