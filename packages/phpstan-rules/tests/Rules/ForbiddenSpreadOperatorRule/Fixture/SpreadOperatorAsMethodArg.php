<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenSpreadOperatorRule\Fixture;

final class SpreadOperatorAsMethodArg
{
    public function __construct(...$args)
    {
    }
}
