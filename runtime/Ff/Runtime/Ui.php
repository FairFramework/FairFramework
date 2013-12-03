<?php

namespace Ff\Runtime;

use Ff\Lib\Bus;

class Ui
{
    /**
     *
     * @var Bus
     */
    private $bus;

    private $contentType;
    
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
        $this->contentType = $this->bus->context()->getParam('content_type', 'html');
    }

    /**
     * @return \Ff\Lib\Ui\Title
     */
    public function title()
    {
        return $this->bus->getInstance("ui/title");
    }

    /**
     * @return \Ff\Lib\Ui\Text
     */
    public function text()
    {
        return $this->bus->getInstance("ui/text");
    }
}
