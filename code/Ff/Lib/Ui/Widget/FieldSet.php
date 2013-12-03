<?php

namespace Ff\Lib\Widget;

use Ff\Lib\Widget;

class FieldSet extends Widget
{
    public function load()
    {
        //
    }

    public function render()
    {
        $result = '';
        foreach ($this->config['fields'] as $field => $config) {
            $result .= '<div class="input-group">';
            $result .= '<span class="input-group-addon">'.$config['label'].'</span>';
            $result .= '<input class="form-control" name="'.$config['name'].'" value="'.$this->getData($field).'" />';
            $result .= '</div>';
            $result .= '<br />';
        }
        $result .= '';
        return $result;
    }
}
