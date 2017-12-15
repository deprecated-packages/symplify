<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\GitWrapper\Event\GitErrorEvent;
use Symplify\GitWrapper\Event\GitEvent;
use Symplify\GitWrapper\Event\GitPrepareEvent;
use Symplify\GitWrapper\Event\GitSuccessEvent;

final class TestSubscriber implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private $calledMethods = [];

    /**
     * @var GitEvent
     */
    private $gitEvent;

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GitPrepareEvent::class => 'onPrepare',
            GitSuccessEvent::class => 'onSucess',
            GitErrorEvent::class => 'onError',
        ];
    }

    public function onPrepare(GitEvent $gitEvent): void
    {
        $this->calledMethods[] = 'onPrepare';
        $this->gitEvent = $gitEvent;
    }

    public function onSuccess(GitEvent $gitEvent): void
    {
        $this->calledMethods[] = 'onSuccess';
        $this->gitEvent = $gitEvent;
    }

    public function onError(GitEvent $gitEvent): void
    {
        $this->calledMethods[] = 'onError';
        $this->gitEvent = $gitEvent;
    }

    public function wasMethodCalled(string $method): bool
    {
        return in_array($method, $this->calledMethods);
    }

    public function getEvent(): GitEvent
    {
        return $this->gitEvent;
    }
}
