<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Bus;
use Ff\Lib\Data;
use Ff\Lib\Render\Html\Template\Stream;
use Ff\Lib\Render\Html\Template\Transport;

abstract class AbstractElement
{
    /**
     *
     * @var Bus
     */
    protected $bus;

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
    }

    public function render(Data $data, Data $attributes, Data $globalData)
    {
        $template = isset($attributes->uiTemplate)
            ? $attributes->uiTemplate
            : $this->config->get('attributes/template');
        $path = DIR_CODE . 'Ff/Design/' . $globalData->uiTheme . '/Template/' . $template . '.php';

        return $this->renderTemplate($path, $data, $attributes);
    }

    protected function renderTemplate($path, Data $data, Data $attributes)
    {
        $stream = new Stream();

        if ($attributes->resource) {
            $resourceIdentity = $attributes->resource;
            $resource = $this->bus->getInstance($resourceIdentity);
            $dataToRender = $resource->getData();
            Transport::set($path, $dataToRender);
            return $stream->render($path);
        } elseif ($attributes->dataCollection) {
            $resourceName = $attributes->dataCollection;
            $dataToRender = $data->get($resourceName);
            $result = '';
            foreach ($dataToRender as $dataItem) {
                Transport::set($path, $dataItem);
                $result .= $stream->render($path);
            }
            return $result;
        } else {
            Transport::set($path, $data);
            return $stream->render($path);
        }

        return '';
    }

    protected function getAttributesHtml(array $attributes)
    {
        $html = array();
        foreach ($attributes as $name => $value) {
            $html[] = $name . '=' . '"' . $value . '"';
        }
        return implode(' ', $html);
    }

    protected function getAttribute(\SimpleXMLElement $element, $name)
    {
        $attributes = $element->attributes();
        return isset($attributes[$name]) ? (string)$attributes[$name] : null;
    }
}
