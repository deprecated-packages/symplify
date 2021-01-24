<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\Fixture;

final class SkipCount
{
    public function run($items)
    {
        return sprintf('Found %d missing templates', count($items));
    }
}
