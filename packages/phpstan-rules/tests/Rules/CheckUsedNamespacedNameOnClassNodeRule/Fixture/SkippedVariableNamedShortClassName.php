<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckUsedNamespacedNameOnClassNodeRule\Fixture;

use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

final class SkippedVariableNamedShortClassName
{
    public function process(Class_ $class, Scope $scope): array
    {
        $shortClassName = (string) $class->name;

        return [];
    }
}
