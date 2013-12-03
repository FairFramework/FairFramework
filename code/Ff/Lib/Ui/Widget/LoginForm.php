<?php

namespace Ff\Lib\Widget;

use Ff\Lib\Widget\FieldSet;

class LoginForm extends FieldSet
{
    public function load()
    {
        $this->config = array(
            'label' => 'Login Form',
            'fields' => array(
                'username' => array(
                    'label' => 'User Name',
                    'name' => 'username',
                    'value' => ''
                ),
                'password' => array(
                    'label' => 'Password',
                    'name' => 'password',
                    'value' => ''
                )
            )
        );
    }
}
