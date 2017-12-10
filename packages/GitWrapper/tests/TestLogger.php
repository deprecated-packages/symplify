<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests;

use Psr\Log\AbstractLogger;

/**
 * Intercepts data sent to STDOUT and STDERR and uses the echo construct to
 * output the data so we can capture it using normal output buffering.
 */
final class TestLogger extends AbstractLogger
{
    public $messages = [];

    public $levels = [];

    public $contexts = [];

    public function log($level, $message, array $context = []): void
    {
        $this->messages[] = $message;
        $this->levels[] = $level;
        $this->contexts[] = $context;
    }

    public function clearMessages(): void
    {
        $this->messages = $this->levels = $this->contexts = [];
    }
}
