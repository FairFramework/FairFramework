<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Bus;
use Ff\Lib\Data;
use Ff\Lib\Render\Html\Stream;
use Ff\Lib\Render\Html\Template\Transport;
use Ff\Lib\Render\Html\Template;

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

    public function prepare(\SimpleXMLElement $sourceElement, $localRefPrefix = null)
    {
        $template = $this->getAttribute($sourceElement, 'uiTemplate')
            ? $this->getAttribute($sourceElement, 'uiTemplate')
            : $this->config->get('attributes/template');
        $path = DIR_CODE . 'Ff/Design/' . Transport::get('global/uiTheme') . '/Template/' . $template . '.php';

        $uiTemplate = new Template($this->bus);
        $uiTemplate->load($path);

        $uiTemplate->extend($sourceElement);

        $uiTemplate->prepare($localRefPrefix);

        return $uiTemplate->getRoot();
    }

    public function render(\SimpleXMLElement $element, Data $data, Data $globalData)
    {
        $template = $this->getAttribute($element, 'uiTemplate')
            ? $this->getAttribute($element, 'uiTemplate')
            : $this->config->get('attributes/template');
        $path = DIR_CODE . 'Ff/Design/' . $globalData->uiTheme . '/Template/' . $template . '.php';

        return $this->renderTemplate($path, $data, $element);
    }

    protected function renderTemplate($path, Data $data, \SimpleXMLElement $element)
    {
        $stream = new Stream();

        if ($this->getAttribute($element, 'resource')) {
            $resourceIdentity = $this->getAttribute($element, 'resource');
            $resource = $this->bus->getInstance($resourceIdentity);
            $dataToRender = $resource->getData();
            Transport::set($path, $dataToRender);
            return $stream->render($path);
        } elseif ($this->getAttribute($element, 'dataCollection')) {
            $resourceName = $this->getAttribute($element, 'dataCollection');
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
