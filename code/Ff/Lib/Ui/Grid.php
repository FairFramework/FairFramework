<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Data;
use Ff\Lib\Ui\AbstractElement;

class Grid extends AbstractElement
{
    public function render(\SimpleXMLElement $element, Data $data, Data $globalData)
    {
        $result = '<table ' . $this->getAttributesHtml($element) . '>';
        foreach ($data as $rowId => $item) {
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
