<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Reflection\PropertyReflection;
use ReflectionMethod;
use Symplify\Astral\Reflection\ReflectionParser;
use Symplify\PHPStanRules\PhpDoc\AnnotationAttributeDetector;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\PHPStanRules\Reflection\PropertyAnalyzer;

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
        private PropertyAnalyzer $propertyAnalyzer,
        private AnnotationAttributeDetector $annotationAttributeDetector,
        private ReflectionParser $reflectionParser,
        private NodeFinder $nodeFinder,
        private NodeComparator $nodeComparator
    ) {
    }

    /**
     * @param ClassReflection[] $parentClassReflections
     */
    public function isParentInjectPropertyFetch(
        PropertyFetch $propertyFetch,
        Scope $scope,
        array $parentClassReflections
    ): bool {
        $propertyFetchName = $propertyFetch->name;
        if (! $propertyFetchName instanceof Identifier) {
            return false;
        }

        $propertyName = $propertyFetchName->name;

        foreach ($parentClassReflections as $parentClassReflection) {
            if (! $parentClassReflection->hasNativeProperty($propertyName)) {
                continue;
            }

            /** @var PhpPropertyReflection $propertyReflection */
            $propertyReflection = $parentClassReflection->getProperty($propertyName, $scope);

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
        if (\str_starts_with($methodName, 'inject')) {
            return true;
        }

        return $this->annotationAttributeDetector->hasNodeAnnotationOrAttribute(
            $classMethod,
            self::INJECT,
            self::INJECT_ATTRIBUTE_CLASS
        );
    }

    private function hasPropertyReflectionInjectAnnotationAttribute(
        PropertyReflection $propertyReflection,
        PropertyFetch $propertyFetch,
        ClassReflection $classReflection
    ): bool {
        if (! $propertyReflection instanceof PhpPropertyReflection) {
            return false;
        }

        $property = $this->reflectionParser->parsePropertyReflection($propertyReflection->getNativeReflection());
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
        $nativeClassReflection = $classReflection->getNativeReflection();

        $reflectionMethods = $nativeClassReflection->getMethods(ReflectionMethod::IS_PUBLIC);
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
