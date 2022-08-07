<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoEmptyClassRule\NoEmptyClassRuleTest
 */
final class NoEmptyClassRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'There should be no empty class';

    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
    }

    /**
     * @return class-string<Node>
     */
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
        if ($classLike->stmts !== []) {
            return [];
        }

        // skip attribute
        if ($this->isAttributeClass($node)) {
            return [];
        }

        if ($this->shouldSkipClassLike($classLike, $node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function getSome()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipClassLike(ClassLike $classLike, InClassNode $inClassNode): bool
    {
        if (! $classLike instanceof Class_ && ! $classLike instanceof Trait_) {
            return true;
        }

        $classReflection = $inClassNode->getClassReflection();
        if ($classReflection->isSubclassOf(Throwable::class)) {
            return true;
        }

        if ($classLike->getComments() !== []) {
            return true;
        }

        return $this->isFinalClassWithAbstractOrInterfaceParent($classLike);
    }

    private function isFinalClassWithAbstractOrInterfaceParent(Class_ | Trait_ $classLike): bool
    {
        if (! $classLike instanceof Class_) {
            return false;
        }

        if ($classLike->implements !== []) {
            return true;
        }

        if (! $classLike->isFinal()) {
            return false;
        }

        if (! $classLike->extends instanceof Name) {
            return false;
        }

        $parentClass = $classLike->extends->toString();
        $parentClassReflection = $this->reflectionProvider->getClass($parentClass);

        return $parentClassReflection->isAbstract();
    }

    private function isAttributeClass(InClassNode $inClassNode): bool
    {
        $classReflection = $inClassNode->getClassReflection();
        return $classReflection->isAttributeClass();
    }
}
