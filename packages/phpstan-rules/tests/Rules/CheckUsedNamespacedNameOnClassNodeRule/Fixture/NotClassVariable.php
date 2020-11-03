<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckUsedNamespacedNameOnClassNodeRule\Fixture;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;

final class NotClassVariable
{
    public function process(ClassMethod $node, Scope $scope): array
    {
        $node;
        return [];
    }
}
