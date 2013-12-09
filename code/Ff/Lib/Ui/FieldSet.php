<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Data;
use Ff\Lib\Ui\AbstractElement;

class FieldSet extends AbstractElement
{
    public function render(\SimpleXMLElement $element, Data $data, Data $globalData)
    {
        $result = '<div ' . $this->getAttributesHtml($element) . '>';
        foreach ($data as $field => $value) {
            $result .= '<div>';
            $result .= '<span class="input-group-addon">' . $value->label . '</span>';
            $result .= '<input class="form-control" name="' . $value->name . '" value="' . $value->value .'" />';
            $result .= '</div>';
            $result .= '<br />';
        }
        $result .= '</div>';
        return $result;
    }
}
