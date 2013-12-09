<?php

namespace Ff\Lib\Resource\Content;

use Ff\Lib\Data;
use Ff\Lib\Resource;

class Menu extends Resource
{
    protected $code = 'menu';

    public function load($identity)
    {
        $this->data = $this->getMenuData($identity);
    }

    private function getMenuData($identity)
    {
        $menu = array(
            'topmenu' => array(
                'title' => 'Top Menu',
                'items' => array(
                    array(
                        'title' => 'Dashboard',
                        'uri' => 'dashboard'
                    ),
                    array(
                        'title' => 'Configuration',
                        'uri' => 'configuration'
                    ),
                    array(
                        'title' => 'Content',
                        'uri' => 'content'
                    )
                )
            )
        );

        $data = isset($menu[$identity]) ? $menu[$identity] : array();
        return new Data($data);
    }
}