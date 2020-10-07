<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckUsedNamespacedNameOnClassNodeRule\Fixture;

use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

final class UsedNamespacedClassRule
{
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function process(Class_ $class, Scope $scope): array
    {
        $class->namespacedName;
        return [];
    }
}
