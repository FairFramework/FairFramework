<?php

namespace Ff\Lib\Widget;

use Ff\Lib\Widget;

class Grid extends Widget
{
    public function load()
    {
        //
    }

    public function render()
    {
        $result = '<table>';
        foreach ($this->data as $id => $item) {
            $result .= '<tr item-id="'.$id.'">';
            foreach ($this->config['fields'] as $field => $config) {
                $result .= '<td item-name="'.$field.'">'.$this->getData($field).'</td>';
            }
            $result .= '</tr>';
        }
        $result .= '</table>';
        return $result;
    }
}
