<?php

declare(strict_types=1);

namespace Symplify\Amnesia\Functions;

function env(string $value): string
{
    return '%env(' . $value  . ')%';
}
