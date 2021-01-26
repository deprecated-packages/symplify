<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\Reflection\ClassReflection;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireChildClassGenericTypeRule\RequireChildClassGenericTypeRuleTest
 */
final class RequireChildClassGenericTypeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parent class has defined generic types, so they must be defined here too';

    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    public function __construct(PrivatesCaller $privatesCaller)
    {
        $this->privatesCaller = $privatesCaller;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [InClassNode::class];
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if ($classReflection->isAbstract()) {
            return [];
        }

        if (! $this->hasParentClassWithTemplateTags($classReflection)) {
            return [];
        }

        if ($this->hasExtendsTag($classReflection)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass extends AbstractParentWithGeneric
{
}

/**
 * @template T of Some
 */
abstract class AbstractParentWithGeneric
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
/**
 * @template T of SpecificSome
 * @extends AbstractParentWithGeneric<T>
 */
final class SomeClass extends AbstractParentWithGeneric
{
}

/**
 * @template T of Some
 */
abstract class AbstractParentWithGeneric
{
}
CODE_SAMPLE
            ),
        ]);
    }

    private function hasExtendsTag(ClassReflection $classReflection): bool
    {
        $resolvedPhpDoc = $this->privatesCaller->callPrivateMethod($classReflection, 'getResolvedPhpDoc', []);
        if (! $resolvedPhpDoc instanceof ResolvedPhpDocBlock) {
            return false;
        }

        return $resolvedPhpDoc->getExtendsTags() !== [];
    }

    private function hasParentClassWithTemplateTags(ClassReflection $classReflection): bool
    {
        $parentClassReflection = $classReflection->getParentClass();
        if ($parentClassReflection === false) {
            return false;
        }

        return $parentClassReflection->getTemplateTags() !== [];
    }
}
