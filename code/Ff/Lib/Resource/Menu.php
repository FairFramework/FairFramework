<?php

namespace Ff\Lib\Resource;

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
                        'title' => 'Item 1',
                        'has_items' => true,
                        'items' => array(
                            array(
                                'title' => 'Sub Item 1'
                            ),
                            array(
                                'title' => 'Sub Item 2'
                            )
                        )
                    ),
                    array(
                        'title' => 'Edit',
                        'command' => 'edit'
                    )
                )
            )
        );

        $data = isset($menu[$identity]) ? $menu[$identity] : array();
        return new Data($data);
    }
}