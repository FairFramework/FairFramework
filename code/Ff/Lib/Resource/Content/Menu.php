<?php

namespace Ff\Lib\Resource\Content;

use Ff\Lib\Data;
use Ff\Lib\Resource;

class Menu extends Resource
{
    protected $code = 'menu';

    protected function load()
    {
        $this->data = $this->getMenuData($this->identity);
    }

    private function getMenuData($identity)
    {
        $menu = array(
            'resource/content/menu' => array(
                'label' => 'Menus',
                'items' => array(
                    array(
                        'label' => 'Top Menu',
                        'uri' => 'resource/content/menu/topmenu'
                    ),
                    array(
                        'label' => 'Bottom Menu',
                        'uri' => 'resource/content/menu/bottommenu'
                    ),
                    array(
                        'label' => 'Promo Menu',
                        'uri' => 'resource/content/menu/promomenu'
                    )
                )
            ),
            'resource/content/menu/topmenu' => array(
                'label' => 'Top Menu',
                'attributes' => array(
                    'items' => array(
                        'type' => array(
                            'label' => 'Type',
                            'value' => 'nav'
                        )
                    )
                ),
                'items' => array(
                    array(
                        'label' => 'Dashboard',
                        'uri' => 'resource/dashboard'
                    ),
                    array(
                        'label' => 'Configuration',
                        'uri' => 'resource/configuration'
                    ),
                    array(
                        'label' => 'Content',
                        'uri' => 'resource/content'
                    )
                )
            )
        );

        $data = isset($menu[$identity]) ? $menu[$identity] : array();
        return new Data($data);
    }
}