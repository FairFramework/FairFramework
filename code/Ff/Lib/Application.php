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

    /**
     * @param Bus $bus
     */
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    /**
     *
     */
    public function start()
    {
        $context = $this->bus->context();

        $command = $context->getParam('command', self::COMMAND_VIEW);

        $result = null;
        switch ($command) {
            case self::COMMAND_CREATE:
                $uri = $this->bus->command()->create()->execute();
                $this->redirect($uri);
                break;
            case self::COMMAND_DELETE:
                $uri = $this->bus->command()->delete()->execute();
                $this->redirect($uri);
                break;
            case self::COMMAND_UPDATE:
                $uri = $this->bus->command()->update()->execute();
                $this->redirect($uri);
                break;
            case self::COMMAND_SET:
                $uri = $this->bus->command()->set()->execute();
                $this->redirect($uri);
                break;
            case self::COMMAND_VIEW:
                $resource = $this->bus->command()->view()->execute($context);
                $this->render($resource);
                break;
        }
    }

    /**
     * @param $uri
     */
    public function redirect($uri)
    {
        //
    }

    /**
     * @param Resource $resource
     */
    public function render(Resource $resource)
    {
        $context = $this->bus->context();

        $type = $context->getParam('render_type', 'html');

        $result = $this->bus->render()->$type()->render($resource);
        // to have ability setting headers on application level
        echo $result;
    }
}
