<?php

namespace Ff\Lib\Render\Html\Template;

class Transport
{
    private static $data = array();
    
    public static function set($key, $data)
    {
        self::$data[$key] = $data;
    }
    
    public static function get($key)
    {
        return isset(self::$data[$key]) ? self::$data[$key] : null;
    }
}