<?php

namespace Symplify\SymfonyEventDispatcher\Tests\NetteEvent;

use Symfony\Component\EventDispatcher\Event;

class EventStateStorage
{
    /**
     * @var string[]
     */
    private $storage;

    /**
     * @param string $event
     * @param Event $state
     */
    public function addEventState($event, Event $state)
    {
        $this->storage[$event] = $state;
    }

    /**
     * @param string $event
     *
     * @return Event
     */
    public function getEventState($event)
    {
        if (isset($this->storage[$event])) {
            return $this->storage[$event];
        }

        return false;
    }
}
