<?php

declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Contract\Loggable;

interface LoggerInterface
{
    public function process(string $message);
}
