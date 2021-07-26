<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

use Closure;

final class SkipIIFE
{
    public function run(string $value)
    {
        return (function () use ($value) {
           return $value;
        })();
    }
}
