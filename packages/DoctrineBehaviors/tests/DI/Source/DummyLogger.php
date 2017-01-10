<?php declare(strict_types=1);
namespace Zenify\DoctrineBehaviors\Tests\DI\Source;

use Zenify\DoctrineBehaviors\Contract\Loggable\LoggerInterface;

final class DummyLogger implements LoggerInterface
{

    public function process(string $message)
    {
    }
}
