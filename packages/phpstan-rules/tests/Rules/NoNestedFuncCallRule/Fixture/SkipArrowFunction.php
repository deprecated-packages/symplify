<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\Fixture;

final class SkipArrowFunction
{
    public function run($classNames)
    {
        return array_filter($classNames, fn (string $className): bool => str_contains($className, '\\'));
    }
}
