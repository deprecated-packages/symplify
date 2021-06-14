<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Reflection\PropertyReflection;
use ReflectionClass;
use ReflectionMethod;
use Symplify\PHPStanRules\PhpDoc\AnnotationAttributeDetector;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\PHPStanRules\Reflection\Parser\ReflectionParser;
use Symplify\PHPStanRules\Reflection\PropertyAnalyzer;

final class NetteInjectAnalyzer
{
    /**
     * @var string
     */
    private const INJECT = '@inject';

    public function __construct(
        private PropertyAnalyzer $propertyAnalyzer,
        private AnnotationAttributeDetector $annotationAttributeDetector,
        private ReflectionParser $reflectionParser,
        private NodeFinder $nodeFinder,
        private NodeComparator $nodeComparator
    ) {
    }

    public function isParentInjectPropertyFetch(PropertyFetch $propertyFetch, Scope $scope): bool
    {
        $propertyReflection = $this->propertyAnalyzer->matchPropertyReflection($propertyFetch, $scope);

        if (! $propertyReflection instanceof PropertyReflection) {
            return false;
        }

        if ($this->hasPropertyReflectionInjectAnnotationAttribute($propertyReflection)) {
            return true;
        }

        $declaringClassReflection = $propertyReflection->getDeclaringClass();

        $currentClassReflection = $scope->getClassReflection();
        if (! $currentClassReflection instanceof ClassReflection) {
            return false;
        }

        // is defined in another class
        foreach ($declaringClassReflection->getAncestors() as $ancestorClassReflection) {
            if ($currentClassReflection->getName() === $ancestorClassReflection->getName()) {
                continue;
            }

            $nativeClassReflection = $ancestorClassReflection->getNativeReflection();
            if ($this->isPropertyInjectedInClassMethod($nativeClassReflection, $propertyFetch)) {
                return true;
            }
        }

        return false;
    }

    public function isInjectProperty(Property $property): bool
    {
        if (! $property->isPublic()) {
            return false;
        }

        return $this->annotationAttributeDetector->hasNodeAnnotationOrAttribute(
            $property,
            self::INJECT,
            'Nette\DI\Attributes\Inject'
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
            'Nette\DI\Attributes\Inject'
        );
    }

    private function hasPropertyReflectionInjectAnnotationAttribute(PropertyReflection $propertyReflection): bool
    {
        if (! $propertyReflection instanceof PhpPropertyReflection) {
            return false;
        }

        $property = $this->reflectionParser->parsePropertyReflectionToProperty(
            $propertyReflection->getNativeReflection()
        );
        if (! $property instanceof Property) {
            return false;
        }

        return $this->isInjectProperty($property);
    }

    private function isPropertyInjectedInClassMethod(
        ReflectionClass $reflectionClass,
        PropertyFetch $propertyFetch
    ): bool {
        $publicNativeClassMethodReflections = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($publicNativeClassMethodReflections as $nativeMethodReflection) {
            $classMethod = $this->reflectionParser->parseMethodReflectionToClassMethod($nativeMethodReflection);
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
