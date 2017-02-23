<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Loggable;

use Zenify\DoctrineBehaviors\Contract\Loggable\LoggerInterface;

final class LoggerCallable
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(string $message): void
    {
        $this->logger->process($message);
    }
}
