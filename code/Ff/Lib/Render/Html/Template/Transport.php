<?php

namespace Ff\Lib\Render\Html\Template;

use Ff\Lib\Data;

class Transport
{
    static $data;

    private static function getData()
    {
        if (!isset(self::$data)) {
            self::$data = new Data();
        }

        return self::$data;
    }
    public static function set($key, $value)
    {
        $data = self::getData();
        $data->__set($key, $value);
    }


    public static function get($key, $default = null)
    {
        $data = self::getData();

        return $data->get($key, $default);
    }
}
