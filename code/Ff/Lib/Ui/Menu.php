<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Data;
use Ff\Lib\Render\Html\Template\Stream;
use Ff\Lib\Render\Html\Template\Transport;
use Ff\Lib\Ui\AbstractElement;

class Menu extends AbstractElement
{
    public function render(Data $data, Data $attributes, Data $globalData)
    {
        $template = isset($attributes->uiTemplate) ? $attributes->uiTemplate : 'Ui/Menu';
        $path = DIR_CODE . 'Ff/Design/' . $globalData->uiTheme . '/Template/' . $template . '.php';
        $stream = new Stream();

        if ($attributes->resource) {
            $resourceName = $attributes->resource;
            $resourceId = $attributes->dataIdentity;
            $resource = $this->bus->getInstance('resource/' . $resourceName);
            $resource->load($resourceId);
            $dataToRender = $resource->getData();
        } else {
            $dataToRender = $data;
        }

        Transport::set($path, $dataToRender);
        return $stream->render($path);
    }
}