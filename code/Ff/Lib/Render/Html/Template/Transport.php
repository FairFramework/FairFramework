<?php

namespace Ff\Lib\Render\Html\Template;

use Ff\Lib\Data;

class Transport
{
    static $data;

    protected static $map = array();

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
        $target = $data->__set($key, $value);

        // caching
        self::$map[$key] = $target;
    }


    public static function get($key, $default = null)
    {
        if (isset(self::$map[$key])) {
            // caching
            return self::$map[$key];
        }

        $data = self::getData();

        return $data->get($key, $default);
    }
}
