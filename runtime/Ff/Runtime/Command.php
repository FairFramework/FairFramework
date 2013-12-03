<?php

namespace Ff\Runtime;

use Ff\Lib\Data\String;
use Ff\Lib\Bus;

class Command
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

    /**
     * 
     * @return \Ff\Lib\Command\Create
     */
    public function create()
    {
        return $this->bus->getInstance('command/create');
    }

    /**
     * 
     * @return \Ff\Lib\Command\Delete
     */
    public function delete()
    {
        return $this->bus->getInstance('command/delete');
    }

    /**
     * 
     * @return \Ff\Lib\Command\Update
     */
    public function update()
    {
        return $this->bus->getInstance('command/update');
    }

    /**
     * 
     * @return \Ff\Lib\Command\Set
     */
    public  function set()
    {
        return $this->bus->getInstance('command/set');
    }

    /**
     * 
     * @return \Ff\Lib\Command\View
     */
    public function view()
    {
        return $this->bus->getInstance('command/view');
    }
}
