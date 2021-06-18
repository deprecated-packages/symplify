<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Stmt\Property;

final class ProtectedAnalyzer
{
    public function __construct(
        private DependencyNodeAnalyzer $dependencyNodeAnalyzer,
        private TypeNodeAnalyzer $typeNodeAnalyzer,
        private AutowiredMethodAnalyzer $autowiredMethodAnalyzer,
    ) {
    }

    public function isProtectedPropertyOrClassConstAllowed(Property $property): bool
    {
        if ($this->dependencyNodeAnalyzer->isInsideAbstractClassAndPassedAsDependency($property)) {
            return true;
        }

        if ($this->dependencyNodeAnalyzer->isInsideClassAndAutowiredMethod($property)) {
            return true;
        }

        return $this->typeNodeAnalyzer->isStaticAndContainerOrKernelType($property);
    }
}
