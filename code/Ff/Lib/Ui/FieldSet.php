<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Ui\AbstractElement;

class FieldSet extends AbstractElement
{
    public function render($content, array $arguments = array())
    {
        $result = '<div ' . $this->getArgumentsHtml($arguments) . '>';
        foreach ($content as $field => $value) {
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
