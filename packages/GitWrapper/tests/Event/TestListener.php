<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Test\Event;

use Symplify\GitWrapper\Event\GitEvent;

final class TestListener
{
    /**
     * The methods that were called.
     *
     * @var array
     */
    private $methods = [];

    /**
     * The event object passed to the onPrepare method.
     *
     * @var \GitWrapper\Event\GitEvent
     */
    private $event;

    public function methodCalled($method)
    {
        return in_array($method, $this->methods);
    }


    public function getEvent(): \GitWrapper\Event\GitEvent
    {
        return $this->event;
    }

    public function onPrepare(GitEvent $event): void
    {
        $this->methods[] = 'onPrepare';
        $this->event = $event;
    }

    public function onSuccess(GitEvent $event): void
    {
        $this->methods[] = 'onSuccess';
    }

    public function onError(GitEvent $event): void
    {
        $this->methods[] = 'onError';
    }

    public function onBypass(GitEvent $event): void
    {
        $this->methods[] = 'onBypass';
    }
}
