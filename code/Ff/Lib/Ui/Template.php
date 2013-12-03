<?php

namespace Ff\Lib;

use Ff\Api\TemplateInterface;

class Template implements TemplateInterface
{
    protected static $_instance;

    protected $_data;

    protected function __construct(array $data)
    {
        $this->_data = $data;
    }

    public function getClassId()
    {
        return get_class($this);
    }

    protected static function _instance($data)
    {
        if (null === self::$_instance) {
            self::$_instance = new static($data);
        }
        return self::$_instance;
    }

    public static function toHtml(array $data)
    {
        return self::_instance($data)->render();
    }

    public function render()
    {
        return '';
    }

    protected function _renderViaPlugins($method)
    {
        $beforeHtml = '';//$this->$method();

        $html = $this->$method();

        $afterHtml = '';//$this->$method();

        return $beforeHtml . $html . $afterHtml;
    }
}
