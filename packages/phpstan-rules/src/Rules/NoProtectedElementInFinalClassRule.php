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
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ParentGuard\ParentClassMethodGuard;
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
        private SimpleNameResolver $simpleNameResolver,
        private ParentClassMethodGuard $parentClassMethodGuard
    ) {
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]
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

        $propertyErrorMessages = $this->processProperties($classLike->getProperties(), $scope);
        $classMethodErrorMessages = $this->processClassMethods($classLike->getMethods(), $scope);

        return array_merge($propertyErrorMessages, $classMethodErrorMessages);
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

    /**
     * @param ClassMethod[] $classMethods
     * @return RuleError[]
     */
    private function processClassMethods(array $classMethods, Scope $scope): array
    {
        $errorMessages = [];

        foreach ($classMethods as $classMethod) {
            if (! $classMethod->isProtected()) {
                continue;
            }

            if ($this->shouldSkipClassMethod($classMethod, $scope)) {
                continue;
            }

            $errorMessages[] = $this->createErrorMessageWithLine($classMethod);
        }

        return $errorMessages;
    }

    private function shouldSkipClassMethod(ClassMethod $classMethod, Scope $scope): bool
    {
        // is Symfony Kernel required magic method?
        if ($this->isSymfonyMicroKernelRequired($classMethod, $scope)) {
            return true;
        }

        return $this->parentClassMethodGuard->isClassMethodGuardedByParentClassMethod($classMethod, $scope);
    }

    private function shouldSkipProperty(Property $property, Scope $scope): bool
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

    /**
     * @param Property[] $properties
     * @return RuleError[]
     */
    private function processProperties(array $properties, Scope $scope): array
    {
        $errorMessages = [];

        foreach ($properties as $property) {
            if (! $property->isProtected()) {
                continue;
            }

            if ($this->shouldSkipProperty($property, $scope)) {
                continue;
            }

            $errorMessages[] = $this->createErrorMessageWithLine($property);
        }

        return $errorMessages;
    }

    private function createErrorMessageWithLine(Node $node): RuleError
    {
        return RuleErrorBuilder::message(self::ERROR_MESSAGE)
            ->line($node->getLine())
            ->build();
    }
}
