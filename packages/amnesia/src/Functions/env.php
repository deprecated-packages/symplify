<?php

declare(strict_types=1);

namespace Symplify\Amnesia\Functions;

if (! function_exists('Symplify\Amnesia\Functions\env')) {
    function env(string $value): string
    {
        return '%env(' . $value  . ')%';
    }
}
