<?php

namespace Ff\Lib\Render\Html;

use Ff\Api\ContextInterface;
use Ff\Lib\Resource;
use Ff\Lib\Bus;
use Ff\Lib\Render\Template\Transport;

class Page
{
    /**
     *
     * @var Bus
     */
    private $bus;
    
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    public function render(Resource $resource)
    {
        $uiTheme = $resource->config->get('ui_theme', 'default');
        $uiTemplate = $resource->config->get('ui_template', 'staticPage');

        $path = 'design/' . $uiTheme . '/template/' . $uiTemplate .'.php';
        $render = function ($data) use ($path) {
            include 'ff.template://' . $path;
        };
        
        $data = new \Ff\Lib\Data();

        $code = $resource->getCode();
        $data->$code = $resource;

        Transport::set($path, $data);
        $render($data);
    }
}
