<?php declare(strict_types=1);
namespace Symplify\DoctrineBehaviors\Tests\DI\Source;

use Symplify\DoctrineBehaviors\Contract\Loggable\LoggerInterface;

final class DummyLogger implements LoggerInterface
{
    public function process(string $message): void
    {
    }
}
