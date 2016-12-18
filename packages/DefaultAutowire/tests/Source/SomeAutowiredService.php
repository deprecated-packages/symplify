<?php

declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\Source;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SomeAutowiredService
{
    /**
     * @var SomeService
     */
    private $someService;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(SomeService $someService, EventDispatcherInterface $eventDispatcher)
    {
        $this->someService = $someService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return SomeService
     */
    public function getSomeService()
    {
        return $this->someService;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }
}
