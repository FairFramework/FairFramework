<?php

namespace Ff\Lib\Data;

class String
{
    private static $underscoreCache = array();

    public static function toClassPath($name)
    {
        if (isset(self::$underscoreCache[$name])) {
            return self::$underscoreCache[$name];
        }
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1\\$2", $name));
        self::$underscoreCache[$name] = $result;
        return $result;
    }
}