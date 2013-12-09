<?php

namespace Ff\Lib\Ui;

use Ff\Lib\Data;
use Ff\Lib\Render\Html\Template\Transport;
use Ff\Lib\Ui\AbstractElement;

class Menu extends AbstractElement
{
    public function render(\SimpleXMLElement $element, Data $data, Data $globalData)
    {
        if ($this->getAttribute($element, 'data-resource')) {
            $resourceName = (string)$this->getAttribute($element, 'data-resource');
            $resourceId = (string)$this->getAttribute($element, 'data-identity');
            $resource = $this->bus->getInstance('resource/' . $resourceName);
            $resource->load($resourceId);
            $dataToRender = $resource->getData();
        } else {
            $dataToRender = $data;
        }

        if ($this->getAttribute($element, 'ui-template')) {
            $template = (string)$this->getAttribute($element, 'ui-template');
        } else {
            $template = 'Ui/Menu';
        }

        $path = DIR_CODE . 'Ff/Design/' . $globalData->uiTheme . '/Template/' . $template . '.php';

        $render = function ($data) use ($path) {
            include 'ff.template.phtml://' . $path;
        };

        Transport::set($path, $dataToRender);

        $render($dataToRender);
    }
}
