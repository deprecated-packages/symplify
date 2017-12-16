<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\EventSubscriber\Source;

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
            GitSuccessEvent::class => 'onSuccess',
            GitErrorEvent::class => 'onError',
        ];
    }

    public function onPrepare(GitPrepareEvent $gitPrepareEvent): void
    {
        $this->calledMethods[] = 'onPrepare';
        $this->gitEvent = $gitPrepareEvent;
    }

    public function onSuccess(GitSuccessEvent $gitSuccessEvent): void
    {
        $this->calledMethods[] = 'onSuccess';
        $this->gitEvent = $gitSuccessEvent;
    }

    public function onError(GitErrorEvent $gitErrorEvent): void
    {
        $this->calledMethods[] = 'onError';
        $this->gitEvent = $gitErrorEvent;
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
