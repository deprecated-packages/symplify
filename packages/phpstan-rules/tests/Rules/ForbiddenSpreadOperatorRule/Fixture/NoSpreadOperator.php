<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

final class NoSpreadOperator
{
    public function __construct(array $args)
    {
        var_dump($args);
    }
}
