<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PackageBuilder\ValueObject\MethodName;

final class DependencyNodeAnalyzer
{
    public function __construct(
        private NodeFinder $nodeFinder,
        private SimpleNameResolver $simpleNameResolver,
        private SimpleNodeFinder $simpleNodeFinder,
        private AutowiredMethodPropertyAnalyzer $autowiredMethodPropertyAnalyzer
    ) {
    }

    public function isInsideAbstractClassAndPassedAsDependency(Property $property, Class_ $class): bool
    {
        if (! $class->isAbstract()) {
            return false;
        }

        $classMethod = $class->getMethod(MethodName::CONSTRUCTOR) ?? $class->getMethod(MethodName::SET_UP);
        if (! $classMethod instanceof ClassMethod) {
            return false;
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf($classMethod, Assign::class);
        if ($assigns === []) {
            return false;
        }

        return $this->isBeingAssignedInAssigns($property, $assigns);
    }

    public function isInsideClassAndAutowiredMethod(Property $property, Class_ $class): bool
    {
        /** @var string $propertyName */
        $propertyName = $this->simpleNameResolver->getName($property);

        /** @var PropertyFetch[] $propertyFetches */
        $propertyFetches = $this->simpleNodeFinder->findByType($class, PropertyFetch::class);
        foreach ($propertyFetches as $propertyFetch) {
            if (! $this->simpleNameResolver->isName($propertyFetch->name, $propertyName)) {
                continue;
            }

            // is inside autowired class method?
            $classMethod = $this->simpleNodeFinder->findFirstParentByType($propertyFetch, ClassMethod::class);
            if (! $classMethod instanceof ClassMethod) {
                continue;
            }

            if ($this->autowiredMethodPropertyAnalyzer->detect($classMethod)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Assign[] $assigns
     */
    private function isBeingAssignedInAssigns(Property $property, array $assigns): bool
    {
        foreach ($assigns as $assign) {
            if (! $assign->var instanceof PropertyFetch) {
                continue;
            }

            if ($this->isPropertyFetchAndPropertyMatch($assign->var, $property)) {
                return true;
            }
        }

        return false;
    }

    private function isPropertyFetchAndPropertyMatch(PropertyFetch $propertyFetch, Property $property): bool
    {
        $assignedPropertyName = $this->simpleNameResolver->getName($property);
        if ($assignedPropertyName === null) {
            return false;
        }

        if (! $this->isLocalPropertyFetch($propertyFetch)) {
            return false;
        }

        $propertyName = $this->simpleNameResolver->getName($propertyFetch->name);
        if ($propertyName === null) {
            return false;
        }

        return $propertyName === $assignedPropertyName;
    }

    private function isLocalPropertyFetch(PropertyFetch $propertyFetch): bool
    {
        if (! $propertyFetch->var instanceof Variable) {
            return false;
        }

        $propertyVariableName = $this->simpleNameResolver->getName($propertyFetch->var);
        return $propertyVariableName === 'this';
    }
}
