<?php

namespace Ff\Lib;

use Ff\Lib\Context;
use Ff\Lib\Resource;

class Application
{
    const MODE_PRODUCTION = 'production';

    const MODE_DEVELOPMENT = 'development';

    const MODE_SAFE = 'safe';

    const COMMAND_CREATE = 'create';

    const COMMAND_DELETE = 'delete';

    const COMMAND_UPDATE = 'update';

    const COMMAND_SET = 'set';

    const COMMAND_VIEW = 'view';
    
    /**
     * 
     * @var Bus
     */
    private $bus;
    
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    public function start(Context $context)
    {
        $command = $context->getParam('command', self::COMMAND_VIEW);

        $result = null;
        switch ($command) {
            case self::COMMAND_CREATE:
                $uri = $this->bus->command()->create()->execute($context);
                $this->redirect($context, $result);
                break;
            case self::COMMAND_DELETE:
                $uri = $this->bus->command()->delete()->execute($context);
                $this->redirect($context, $result);
                break;
            case self::COMMAND_UPDATE:
                $uri = $this->bus->command()->update()->execute($context);
                $this->redirect($context, $result);
                break;
            case self::COMMAND_SET:
                $uri = $this->bus->command()->set()->execute($context);
                $this->redirect($context, $result);
                break;
            case self::COMMAND_VIEW:
                $resource = $this->bus->command()->view()->execute($context);
                $this->render($context, $resource);
                break;
        }
    }

    public function redirect(Context $context, $uri)
    {
        //
    }

    public function render(Context $context, Resource $resource)
    {
        $content = $context->getParam('content_type', 'html');
        $ui = $context->getParam('ui_type', 'page');
        $contentRender = $this->bus->getInstance("render/{$content}/{$ui}");

        $contentRender->render($resource);
    }
}
