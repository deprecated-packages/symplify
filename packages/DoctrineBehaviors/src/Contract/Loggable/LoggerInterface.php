<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\Contract\Loggable;

interface LoggerInterface
{
    public function process(string $message): void;
}
