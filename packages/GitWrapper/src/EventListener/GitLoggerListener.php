<?php declare(strict_types=1);

namespace Symplify\GitWrapper\EventListener;

use DomainException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\GitWrapper\Event\AbstractGitEvent;
use Symplify\GitWrapper\Event\GitEvents;
use Symplify\GitWrapper\Event\GitOutputEvent;

final class GitLoggerListener implements EventSubscriberInterface, LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Mapping of event to log level.
     *
     * @var string[]
     */
    private $logLevelMappings = [
        GitEvents::GIT_PREPARE => LogLevel::INFO,
        GitEvents::GIT_OUTPUT => LogLevel::DEBUG,
        GitEvents::GIT_SUCCESS => LogLevel::INFO,
        GitEvents::GIT_ERROR => LogLevel::ERROR,
    ];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Sets the log level mapping for an event.
     *
     * @param string|false $logLevel
     */
    public function setLogLevelMapping(string $eventName, $logLevel): void
    {
        $this->logLevelMappings[$eventName] = $logLevel;
    }

    /**
     * Returns the log level mapping for an event.
     *
     * @throws \DomainException
     */
    public function getLogLevelMapping(string $eventName): string
    {
        if (! isset($this->logLevelMappings[$eventName])) {
            throw new DomainException('Unknown event: ' . $eventName);
        }

        return $this->logLevelMappings[$eventName];
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            GitEvents::GIT_PREPARE => ['onPrepare', 0],
            GitEvents::GIT_OUTPUT => ['handleOutput', 0],
            GitEvents::GIT_SUCCESS => ['onSuccess', 0],
            GitEvents::GIT_ERROR => ['onError', 0],
        ];
    }

    /**
     * Adds a log message using the level defined in the mappings.
     *
     * @throws DomainException
     */
    public function log(AbstractGitEvent $gitEvent, string $message, array $context = [], ?string $eventName = null): void
    {
        $method = $this->getLogLevelMapping($eventName);
        if ($method !== false) {
            $context += ['command' => $gitEvent->getProcess()->getCommandLine()];
            $this->logger->{$method}($message, $context);
        }
    }

    public function onPrepare(AbstractGitEvent $gitEvent, $eventName = null): void
    {
        $this->log($gitEvent, 'Git command preparing to run', [], $eventName);
    }

    public function handleOutput(GitOutputEvent $gitOutputEvent, $eventName = null): void
    {
        $context = ['error' => $gitOutputEvent->isError() ? true : false];
        $this->log($gitOutputEvent, $gitOutputEvent->getBuffer(), $context, $eventName);
    }

    public function onSuccess(AbstractGitEvent $gitEvent, $eventName = null): void
    {
        $this->log($gitEvent, 'Git command successfully run', [], $eventName);
    }

    public function onError(AbstractGitEvent $gitEvent, $eventName = null): void
    {
        $this->log($gitEvent, 'Error running Git command', [], $eventName);
    }
}
