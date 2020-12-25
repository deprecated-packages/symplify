<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\Fixture;

final class SkipNoType
{
    public function run($value = null): void
    {
    }
}
