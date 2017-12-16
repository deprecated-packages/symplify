<?php declare(strict_types=1);

namespace Symplify\GitWrapper\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\GitWrapper\Event\AbstractGitEvent;
use Symplify\GitWrapper\Event\GitErrorEvent;
use Symplify\GitWrapper\Event\GitOutputEvent;
use Symplify\GitWrapper\Event\GitPrepareEvent;
use Symplify\GitWrapper\Event\GitSuccessEvent;

final class GitLoggerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GitPrepareEvent::class => 'onPrepare',
            GitOutputEvent::class => 'handleOutput',
            GitSuccessEvent::class => 'onSuccess',
            GitErrorEvent::class => 'onError',
        ];
    }

    public function onPrepare(AbstractGitEvent $gitEvent): void
    {
        $data = [
            'command' => $gitEvent->getProcess()->getCommandLine(),
            'eventName' => get_class($gitEvent),
        ];

        $this->logger->info('Git command preparing to run', $data);
    }

    public function handleOutput(GitOutputEvent $gitOutputEvent): void
    {
        $data = [
            'command' => $gitOutputEvent->getProcess()->getCommandLine(),
            'eventName' => get_class($gitOutputEvent),
            'error' => $gitOutputEvent->isError(),
        ];

        $this->logger->debug($gitOutputEvent->getBuffer(), $data);
    }

    public function onSuccess(AbstractGitEvent $gitEvent): void
    {
        $data = [
            'command' => $gitEvent->getProcess()->getCommandLine(),
            'eventName' => get_class($gitEvent),
        ];

        $this->logger->info('Git command successfully run', $data);
    }

    public function onError(AbstractGitEvent $gitEvent): void
    {
        $data = [
            'command' => $gitEvent->getProcess()->getCommandLine(),
            'eventName' => get_class($gitEvent),
        ];

        $this->logger->error('Error running Git command', $data);
    }
}
