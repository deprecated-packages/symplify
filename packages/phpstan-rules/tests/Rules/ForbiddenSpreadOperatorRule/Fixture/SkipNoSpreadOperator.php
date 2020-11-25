<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenSpreadOperatorRule\Fixture;

final class SkipNoSpreadOperator
{
    public function __construct(array $args)
    {
        var_dump($args);
    }
}
