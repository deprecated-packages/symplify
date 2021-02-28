<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenComplexFuncCallRule\Fixture;

final class SkipArrayFilterWithoutCallable
{
    public function run(array $items)
    {
        return array_filter($items);
    }
}
