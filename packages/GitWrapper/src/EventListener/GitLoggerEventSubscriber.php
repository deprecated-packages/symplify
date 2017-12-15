<?php declare(strict_types=1);

namespace Symplify\GitWrapper\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\GitWrapper\Event\AbstractGitEvent;
use Symplify\GitWrapper\Event\GitEvents;
use Symplify\GitWrapper\Event\GitOutputEvent;

final class GitLoggerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GitEvents::GIT_PREPARE => ['onPrepare', 0],
            GitEvents::GIT_OUTPUT => ['handleOutput', 0],
            GitEvents::GIT_SUCCESS => ['onSuccess', 0],
            GitEvents::GIT_ERROR => ['onError', 0],
        ];
    }

    public function onPrepare(AbstractGitEvent $gitEvent): void
    {
        $data = [
            'command' => $gitEvent->getProcess()->getCommandLine(),
            'eventClass' => get_class($gitEvent)
        ];

        $this->logger->info('Git command preparing to run', $data);
    }

    public function handleOutput(GitOutputEvent $gitOutputEvent): void
    {
        $data = [
            'command' => $gitOutputEvent->getProcess()->getCommandLine(),
            'eventClass' => get_class($gitOutputEvent),
            'error' => $gitOutputEvent->isError()
        ];

        $this->logger->debug($gitOutputEvent->getBuffer(), $data);
    }

    public function onSuccess(AbstractGitEvent $gitEvent): void
    {
        $data = [
            'command' => $gitEvent->getProcess()->getCommandLine(),
            'eventClass' => get_class($gitEvent)
        ];

        $this->logger->info('Git command successfully run', $data);
    }

    public function onError(AbstractGitEvent $gitEvent): void
    {
        $data = [
            'command' => $gitEvent->getProcess()->getCommandLine(),
            'eventClass' => get_class($gitEvent)
        ];

        $this->logger->error( 'Error running Git command', $data);
    }
}
