<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule\Fixture;

final class SkipFunctionExprName
{
    public function run($name)
    {
        return $name(1, 1, 1);
    }
}
