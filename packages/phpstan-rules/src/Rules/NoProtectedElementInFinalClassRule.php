<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ParentMethodAnalyser;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoProtectedElementInFinalClassRule\NoProtectedElementInFinalClassRuleTest
 */
final class NoProtectedElementInFinalClassRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of protected element in final class use private element or contract method';

    public function __construct(
        private ParentMethodAnalyser $parentMethodAnalyser,
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        if (! $classLike->isFinal()) {
            return [];
        }

        $errorMessages = [];

        foreach ($classLike->getProperties() as $property) {
            if (! $property->isProtected()) {
                continue;
            }

            if ($this->skipProperty($property, $scope)) {
                continue;
            }

            $errorMessages[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($property->getLine())
                ->build();
        }

        foreach ($classLike->getMethods() as $method) {
            if (! $method->isProtected()) {
                continue;
            }

            if ($this->shouldSkipClassMethod($method, $scope)) {
                continue;
            }

            $errorMessages[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($method->getLine())
                ->build();
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    protected function run()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    private function run()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipClassMethod(ClassMethod $classMethod, Scope $scope): bool
    {
        // is Symfony Kernel required magic method?
        if ($this->isSymfonyMicroKernelRequired($classMethod, $scope)) {
            return true;
        }

        $methodName = (string) $classMethod->name;
        return $this->parentMethodAnalyser->hasParentClassMethodWithSameName($scope, $methodName);
    }

    private function skipProperty(Property $property, Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        $propertyName = (string) $property->props[0]->name;

        foreach ($classReflection->getParents() as $parentClassReflection) {
            if ($parentClassReflection->hasProperty($propertyName)) {
                return true;
            }
        }

        return false;
//
//        $extends = $class->extends;
//        if (! $extends instanceof Name) {
//            return false;
//        }
//
//        /** @var string $propertyName */
//        $propertyName = $this->simpleNameResolver->getName($property);
//        return $this->doesPropertyExistInParentClass($extends, $propertyName);
    }

    private function isSymfonyMicroKernelRequired(ClassMethod $classMethod, Scope $scope): bool
    {
        if (! $this->simpleNameResolver->isNames($classMethod, ['configureRoutes', 'configureContainer'])) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $classReflection->hasTraitUse(MicroKernelTrait::class);
    }
}
