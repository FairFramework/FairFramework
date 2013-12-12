<?php

namespace Ff\Lib\Render;

use Ff\Lib\Bus;
use Ff\Lib\Data;
use Ff\Lib\Render\Html\Stream;
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

        stream_register_wrapper('ff.template.html', 'Ff\Lib\Render\Html\Stream');
    }

    public function render(Resource $resource)
    {
        $uiTheme = $resource->config->get('ui_theme', 'Default');
        $uiTemplate = $resource->config->get('attributes/ui_template', 'StaticPage');

        $path = DIR_CODE . 'Ff/Design/' . $uiTheme . '/Template/' . $uiTemplate .'.php';

        $data = array('uiTheme' => $uiTheme);

        Transport::set('global', $data);
        Transport::set('system', $this->getSystemData());
        Transport::set($resource->getCode(), $resource);

        echo '<!DOCTYPE html>' . "\n";

        $stream = new Stream();
        echo $stream->render($path);
    }

    private function getSystemData()
    {
        $data = array(
            'charset' => 'utf-8',
            'lang' => 'en',
            'base_url' => FF_BASE_URL,
            'base_uri' => FF_BASE_URI
        );
        return $data;
    }
}
