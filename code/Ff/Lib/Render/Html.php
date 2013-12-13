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

    /**
     * @param Resource $resource
     * @return string
     */
    public function render(Resource $resource)
    {
        $uiTheme = $resource->config->get('ui_theme', 'Default');
        $uiTemplate = $resource->config->get('attributes/ui_template', 'StaticPage');

        $path = DIR_CODE . 'Ff/Design/' . $uiTheme . '/Template/' . $uiTemplate .'.php';

        $global = array('uiTheme' => $uiTheme);
        Transport::set('global', $global);

        $system = array(
            'charset' => 'utf-8',
            'lang' => 'en',
            'base_url' => FF_BASE_URL,
            'base_uri' => FF_BASE_URI
        );
        Transport::set('system', $system);

        Transport::set($resource->getCode(), $resource);

        $result = '<!DOCTYPE html>' . "\n";

        $stream = new Stream();
        $result .= $stream->render($path);
        return $result;
    }
}
