<?php

namespace Ff\Lib\Render;

use Ff\Lib\Bus;
use Ff\Lib\Data;
use Ff\Lib\Resource;
use Ff\Lib\Render\Html\Template\Transport;

class Html
{
    /**
     *
     * @var Bus
     */
    private $bus;

    /**
     * @var Data
     */
    public $config;

    /**
     * @param Bus $bus
     * @param Data $configuration
     */
    public function __construct(Bus $bus, Data $configuration)
    {
        $this->bus = $bus;
        $this->config = $configuration;

        stream_register_wrapper('ff.template.phtml', 'Ff\Lib\Render\Html\Template\Stream');
    }

    public function render(Resource $resource)
    {
        $uiTheme = $resource->config->get('ui_theme', 'Default');
        $uiTemplate = $resource->config->get('ui_template', 'StaticPage');

        $path = DIR_CODE . 'Ff/Design/' . $uiTheme . '/Template/' . $uiTemplate .'.php';

        $render = function ($data) use ($path) {
            include 'ff.template.phtml://' . $path;
        };
        
        $data = new Data();

        $code = $resource->getCode();
        $data->$code = $resource;

        $data->system = $this->getSystemData();

        $data->uiTheme = $uiTheme;

        Transport::set('globalData', $data);

        $render($data);
    }

    private function getSystemData()
    {
        $data = array(
            'charset' => 'utf-8',
            'lang' => 'en'
        );
        return new Data($data);
    }
}
