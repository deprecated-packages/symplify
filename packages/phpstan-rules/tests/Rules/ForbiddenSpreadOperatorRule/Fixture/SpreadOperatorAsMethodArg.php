<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

final class SpreadOperatorAsMethodArg
{
    public function __construct(...$args)
    {
    }
}
