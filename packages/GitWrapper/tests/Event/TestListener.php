<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\Event;

use Symplify\GitWrapper\Event\GitEvent;

final class TestListener
{
    /**
     * @var string[]
     */
    private $calledMethods = [];

    /**
     * The event object passed to the onPrepare method.
     *
     * @var GitEvent
     */
    private $gitEvent;

    public function methodCalled(string $method): bool
    {
        return in_array($method, $this->calledMethods);
    }

    public function getEvent(): GitEvent
    {
        return $this->gitEvent;
    }

    public function onPrepare(GitEvent $gitEvent): void
    {
        $this->calledMethods[] = 'onPrepare';
        $this->gitEvent = $gitEvent;
    }

    public function onSuccess(GitEvent $gitEvent): void
    {
        $this->calledMethods[] = 'onSuccess';
    }

    public function onError(GitEvent $gitEvent): void
    {
        $this->calledMethods[] = 'onError';
    }
}
