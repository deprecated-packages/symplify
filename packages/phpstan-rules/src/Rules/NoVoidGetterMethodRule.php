<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoVoidGetterMethodRule\NoVoidGetterMethodRuleTest
 */
final class NoVoidGetterMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Getter method must return something, not void';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private SimpleNodeFinder $simpleNodeFinder
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isClass()) {
            return [];
        }

        if ($node->isAbstract()) {
            return [];
        }

        if (! $this->simpleNameResolver->isName($node, 'get*')) {
            return [];
        }

        if (! $this->isVoidReturnClassMethod($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function getData(): void
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function getData(): array
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isVoidReturnClassMethod(ClassMethod $classMethod): bool
    {
        if ($this->hasClassMethodVoidReturnType($classMethod)) {
            return true;
        }

        return ! $this->simpleNodeFinder->hasByTypes($classMethod, [
            Return_::class,
            Yield_::class,
            // possibly unneded contract override
            Throw_::class,
            Node\Stmt\Throw_::class,
        ]);
    }

    private function hasClassMethodVoidReturnType(ClassMethod $classMethod): bool
    {
        if ($classMethod->returnType === null) {
            return false;
        }

        return $this->simpleNameResolver->isName($classMethod->returnType, 'void');
    }
}
