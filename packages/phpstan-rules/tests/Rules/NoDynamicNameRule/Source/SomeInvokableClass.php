<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Source;

final class SomeInvokableClass
{
    public function __invoke(int $value): int
    {
        return 1000 + $value;
    }
}
