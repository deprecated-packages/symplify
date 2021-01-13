<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoEmptyClassRule\NoEmptyClassRuleTest
 */
final class NoEmptyClassRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'There should be no empty class';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(SimpleNameResolver $simpleNameResolver, ReflectionProvider $reflectionProvider)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->reflectionProvider = $reflectionProvider;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class, Trait_::class];
    }

    /**
     * @param Class_|Trait_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->stmts !== []) {
            return [];
        }

        if ($this->shouldSkipClassLike($node)) {
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

    /**
     * @param Class_|Trait_ $classLike
     */
    private function shouldSkipClassLike(ClassLike $classLike): bool
    {
        $className = $this->simpleNameResolver->getName($classLike);
        if ($className === null) {
            return true;
        }

        if (is_a($className, Throwable::class, true)) {
            return true;
        }

        if ($classLike->getComments() !== []) {
            return true;
        }

        return $this->isFinalClassWithAbstractParent($classLike);
    }

    /**
     * @param Class_|Trait_ $classLike
     */
    private function isFinalClassWithAbstractParent(ClassLike $classLike): bool
    {
        if (! $classLike instanceof Class_) {
            return false;
        }

        if (! $classLike->isFinal()) {
            return false;
        }
        if ($classLike->extends === null) {
            return false;
        }

        $parentClass = $this->simpleNameResolver->getName($classLike->extends);
        if ($parentClass === null) {
            return false;
        }

        $parentClassReflection = $this->reflectionProvider->getClass($parentClass);
        return $parentClassReflection->isAbstract();
    }
}
