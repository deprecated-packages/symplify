<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\NetteEvent;

use Symfony\Component\EventDispatcher\Event;

final class EventStateStorage
{
    /**
     * @var Event[]
     */
    private $storage;

    public function addEventState(string $event, Event $state)
    {
        $this->storage[$event] = $state;
    }

    /**
     * @return bool|Event
     */
    public function getEventState(string $event)
    {
        if (isset($this->storage[$event])) {
            return $this->storage[$event];
        }

        return false;
    }
}
