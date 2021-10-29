<?php

declare(strict_types=1);

namespace MyCLabs\Enum;

if (class_exists(\MyCLabs\Enum\Enum::class)) {
    return;
}

abstract class Enum
{
    public function getKey(): string
    {
    }

    public function getValue(): void
    {
    }
}
