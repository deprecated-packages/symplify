<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Stmt\Property;

final class ProtectedAnalyzer
{
    /**
     * @var DependencyNodeAnalyzer
     */
    private $dependencyNodeAnalyzer;

    /**
     * @var TypeNodeAnalyzer
     */
    private $typeNodeAnalyzer;

    public function __construct(DependencyNodeAnalyzer $dependencyNodeAnalyzer, TypeNodeAnalyzer $typeNodeAnalyzer)
    {
        $this->dependencyNodeAnalyzer = $dependencyNodeAnalyzer;
        $this->typeNodeAnalyzer = $typeNodeAnalyzer;
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
