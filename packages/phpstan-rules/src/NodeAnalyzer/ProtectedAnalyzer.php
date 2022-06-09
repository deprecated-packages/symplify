<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;

final class ProtectedAnalyzer
{
    public function __construct(
        private DependencyNodeAnalyzer $dependencyNodeAnalyzer,
        private TypeNodeAnalyzer $typeNodeAnalyzer,
    ) {
    }

    public function isProtectedPropertyOrClassConstAllowed(Property $property, Class_ $class): bool
    {
        if ($this->dependencyNodeAnalyzer->isInsideAbstractClassAndPassedAsDependency($property, $class)) {
            return true;
        }

        if ($this->dependencyNodeAnalyzer->isInsideClassAndAutowiredMethod($property, $class)) {
            return true;
        }

        return $this->typeNodeAnalyzer->isStaticAndContainerOrKernelType($property);
    }
}
