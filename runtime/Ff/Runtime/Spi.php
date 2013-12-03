<?php

namespace Ff\Runtime;

class Spi
{
    public static function getUiElement()
    {
        return new \Ff\Lib\Ui\Element();
    }
}