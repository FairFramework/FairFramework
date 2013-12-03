<?php

namespace Ff\Lib;

class Result
{
    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function __get($key)
    {
        return isset($this->$key) ? $this->$key : null;
    }

    public function set($data)
    {
        foreach ($data as $key => $value)
        {
            $this->__set($key, $value);
        }
    }
}