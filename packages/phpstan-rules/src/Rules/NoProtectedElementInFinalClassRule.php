<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symplify\PHPStanRules\ParentMethodAnalyser;
use Symplify\PHPStanRules\Types\ClassMethodTypeAnalyzer;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoProtectedElementInFinalClassRule\NoProtectedElementInFinalClassRuleTest
 */
final class NoProtectedElementInFinalClassRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use protected element in final class';

    /**
     * @var ParentMethodAnalyser
     */
    private $parentMethodAnalyser;

    /**
     * @var ClassMethodTypeAnalyzer
     */
    private $classMethodTypeAnalyzer;

    public function __construct(
        ParentMethodAnalyser $parentMethodAnalyser,
        ClassMethodTypeAnalyzer $classMethodTypeAnalyzer
    ) {
        $this->parentMethodAnalyser = $parentMethodAnalyser;
        $this->classMethodTypeAnalyzer = $classMethodTypeAnalyzer;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassMethod::class];
    }

    /**
     * @param Property|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $parent instanceof Class_) {
            return [];
        }

        if (! $parent->isFinal()) {
            return [];
        }

        if (! $node->isProtected()) {
            return [];
        }

        if ($node instanceof ClassMethod) {
            return $this->processClassMethod($node, $parent, $scope);
        }

        return $this->processProperty($parent, $node);
    }

    private function isPropertyExistInTraits(Class_ $class, string $propertyName): bool
    {
        /** @var Identifier $name */
        $name = $class->namespacedName;
        $usedTraits = class_uses($name->toString());
        foreach ($usedTraits as $trait) {
            $r = new ReflectionClass($trait);
            if ($r->hasProperty($propertyName)) {
                return true;
            }
        }

        return false;
    }

    private function isMethodExistInTraits(Class_ $class, string $methodName): bool
    {
        /** @var Identifier $name */
        $name = $class->namespacedName;
        $usedTraits = class_uses($name->toString());

        foreach ($usedTraits as $trait) {
            $reflectionClass = new ReflectionClass($trait);
            if ($reflectionClass->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }

    private function isPropertyExistInParentClass(Name $name, string $propertyName): bool
    {
        $reflectionClass = new ReflectionClass((string) $name);
        return $reflectionClass->hasProperty($propertyName);
    }

    /**
     * @return string[]
     */
    private function processClassMethod(ClassMethod $classMethod, Class_ $class, Scope $scope): array
    {
        // is Symfony Kernel required magic method?
        if ($this->isSymfonyMicroKernelRequired($classMethod, $scope)) {
            return [];
        }

        $methodName = (string) $classMethod->name;
        if ($this->isMethodExistInTraits($class, $methodName)
            || $this->parentMethodAnalyser->hasParentClassMethodWithSameName($scope, $methodName)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @return string[]
     */
    private function processProperty(Class_ $class, Property $property): array
    {
        $extends = $class->extends;
        $propertyName = $property->props[0]->name->toString();
        if ($this->isPropertyExistInTraits($class, $propertyName)
            || ($extends && $this->isPropertyExistInParentClass($extends, $propertyName))) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isSymfonyMicroKernelRequired(ClassMethod $classMethod, Scope $scope): bool
    {
        return $this->classMethodTypeAnalyzer->isClassMethodOfNamesAndType(
            $classMethod,
            $scope,
            ['configureRoutes', 'configureContainer'],
            MicroKernelTrait::class
        );
    }
}
