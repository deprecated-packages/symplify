<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
use Symplify\PackageBuilder\ValueObject\MethodName;

final class DependencyNodeAnalyzer
{
    /**
     * @var string
     * @see https://regex101.com/r/gn2P0C/1
     */
    private const REQUIRED_DOCBLOCK_REGEX = '#\*\s+@required\n?#';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    public function __construct(
        NodeFinder $nodeFinder,
        SimpleNameResolver $simpleNameResolver,
        ParentNodeFinder $parentNodeFinder
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->parentNodeFinder = $parentNodeFinder;
    }

    public function isInsideAbstractClassAndPassedAsDependency(Property $property): bool
    {
        $classLike = $this->parentNodeFinder->getFirstParentByType($property, Class_::class);
        if (! $classLike instanceof Class_) {
            return false;
        }

        if (! $classLike->isAbstract()) {
            return false;
        }

        $classMethod = $classLike->getMethod(MethodName::CONSTRUCTOR) ?? $classLike->getMethod(MethodName::SET_UP);
        if (! $classMethod instanceof ClassMethod) {
            return false;
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf($classMethod, Assign::class);
        if ($assigns === []) {
            return false;
        }

        return $this->isBeeingAssignedInAssigns($property, $assigns);
    }

    public function isInsideClassAndAutowiredMethod(Property $property): bool
    {
        $classLike = $this->parentNodeFinder->getFirstParentByType($property, ClassLike::class);
        if (! $classLike instanceof Class_ && ! $classLike instanceof Trait_) {
            return false;
        }

        $shortClassName = (string) $classLike->name;

        $autowireMethodName = 'autowire' . $shortClassName;

        $classMethod = $classLike->getMethod($autowireMethodName);
        if (! $classMethod instanceof ClassMethod) {
            return false;
        }

        if (! $this->hasRequiredAnnotation($classMethod)) {
            return false;
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf($classMethod, Assign::class);
        if ($assigns === []) {
            return false;
        }

        return $this->isBeeingAssignedInAssigns($property, $assigns);
    }

    /**
     * @param Assign[] $assigns
     */
    private function isBeeingAssignedInAssigns(Property $property, array $assigns): bool
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

    private function hasRequiredAnnotation(ClassMethod $classMethod): bool
    {
        $docComment = $classMethod->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        return (bool) Strings::match($docComment->getText(), self::REQUIRED_DOCBLOCK_REGEX);
    }
}
