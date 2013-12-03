<?php

namespace Ff\Lib\Widget;

use Ff\Lib\Widget;

class Message extends Widget
{
    public function load()
    {
        return array();
    }

    public function render()
    {
        $result = '<h3 class="'.$this->data['message_code'].'">'.$this->data['message_text'].' <span class="label">'.$this->data['message_code'].'</span></h3>';
        return $result;
    }
}
