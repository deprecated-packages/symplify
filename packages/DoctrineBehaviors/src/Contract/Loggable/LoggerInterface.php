<?php

declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Contract\Loggable;

interface LoggerInterface
{

    function process(string $message);
}
