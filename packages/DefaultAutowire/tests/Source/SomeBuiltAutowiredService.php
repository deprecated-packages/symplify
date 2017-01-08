<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\Source;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SomeBuiltAutowiredService
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
    public function getSomeService() : SomeService
    {
        return $this->someService;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher() : EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }
}
