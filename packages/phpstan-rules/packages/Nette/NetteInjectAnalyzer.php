<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use ReflectionMethod;
use Symplify\PHPStanRules\PhpDoc\AnnotationAttributeDetector;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\PHPStanRules\Reflection\ReflectionParser;

final class NetteInjectAnalyzer
{
    /**
     * @var string
     */
    private const INJECT = '@inject';

    /**
     * @var string
     */
    private const INJECT_ATTRIBUTE_CLASS = 'Nette\DI\Attributes\Inject';

    public function __construct(
        private AnnotationAttributeDetector $annotationAttributeDetector,
        private ReflectionParser $reflectionParser,
        private NodeFinder $nodeFinder,
        private NodeComparator $nodeComparator,
    ) {
    }

    /**
     * @param ClassReflection[] $parentClassReflections
     */
    public function isParentInjectPropertyFetch(PropertyFetch $propertyFetch, array $parentClassReflections): bool
    {
        $propertyFetchName = $propertyFetch->name;
        if (! $propertyFetchName instanceof Identifier) {
            return false;
        }

        $propertyName = $propertyFetchName->name;

        foreach ($parentClassReflections as $parentClassReflection) {
            if (! $parentClassReflection->hasNativeProperty($propertyName)) {
                continue;
            }

            $propertyReflection = $parentClassReflection->getNativeProperty($propertyName);
            // we can skip annotated like property-read
            if (! $propertyReflection instanceof PhpPropertyReflection) {
                continue;
            }

            if ($this->hasPropertyReflectionInjectAnnotationAttribute(
                $propertyReflection,
                $propertyFetch,
                $parentClassReflection
            )) {
                return true;
            }
        }

        return false;
    }

    public function isInjectProperty(Property $property): bool
    {
        // not possible to inject private property
        if ($property->isPrivate()) {
            return false;
        }

        return $this->annotationAttributeDetector->hasNodeAnnotationOrAttribute(
            $property,
            self::INJECT,
            self::INJECT_ATTRIBUTE_CLASS
        );
    }

    public function isInjectClassMethod(ClassMethod $classMethod): bool
    {
        if (! $classMethod->isPublic()) {
            return false;
        }

        $methodName = $classMethod->name->toString();
        return \str_starts_with($methodName, 'inject');
    }

    private function hasPropertyReflectionInjectAnnotationAttribute(
        PhpPropertyReflection $phpPropertyReflection,
        PropertyFetch $propertyFetch,
        ClassReflection $classReflection
    ): bool {
        $property = $this->reflectionParser->parsePropertyReflection($phpPropertyReflection->getNativeReflection());
        if (! $property instanceof Property) {
            return false;
        }

        if ($this->isInjectProperty($property)) {
            return true;
        }

        return $this->isPropertyInjectedInClassMethod($classReflection, $propertyFetch);
    }

    private function isPropertyInjectedInClassMethod(
        ClassReflection $classReflection,
        PropertyFetch $propertyFetch
    ): bool {
        $nativeReflection = $classReflection->getNativeReflection();

        $reflectionMethods = $nativeReflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($reflectionMethods as $reflectionMethod) {
            if (! str_starts_with($reflectionMethod->getName(), 'inject')) {
                continue;
            }

            $classMethod = $this->reflectionParser->parseMethodReflection($reflectionMethod);
            if (! $classMethod instanceof ClassMethod) {
                continue;
            }

            if ($this->isClassMethodInjectingCurrentProperty($classMethod, $propertyFetch)) {
                return true;
            }
        }

        return false;
    }

    private function isClassMethodInjectingCurrentProperty(ClassMethod $classMethod, PropertyFetch $propertyFetch): bool
    {
        if (! $this->isInjectClassMethod($classMethod)) {
            return false;
        }

        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf((array) $classMethod->stmts, Assign::class);
        foreach ($assigns as $assign) {
            if (! $assign->var instanceof PropertyFetch) {
                continue;
            }

            /** @var PropertyFetch $injectedPropertyFetch */
            $injectedPropertyFetch = $assign->var;

            if ($this->nodeComparator->areNodesEqual($injectedPropertyFetch, $propertyFetch)) {
                return true;
            }
        }

        return false;
    }
}
