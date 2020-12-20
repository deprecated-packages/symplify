<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckUsedNamespacedNameOnClassNodeRule\Fixture;

use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

final class SkipCompare
{
    public function run(Class_ $class)
    {
        if ($class->name === null) {
            return null;
        }
    }
}
