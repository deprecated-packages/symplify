<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
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

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var SimpleNodeFinder
     */
    private $simpleNodeFinder;

    public function __construct(SimpleNameResolver $simpleNameResolver, SimpleNodeFinder $simpleNodeFinder)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->simpleNodeFinder = $simpleNodeFinder;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Function_::class];
    }

    /**
     * @param ClassMethod|Function_ $node
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

        if (! $this->simpleNameResolver->isName($node, 'get*')) {
            return [];
        }

        if (! $this->isVoidReturnFunctionLike($node)) {
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

    /**
     * @param ClassMethod|Function_ $functionLike
     */
    private function isVoidReturnFunctionLike(FunctionLike $functionLike): bool
    {
        if ($this->hasVoidReturnType($functionLike)) {
            return true;
        }

        return ! $this->simpleNodeFinder->hasByTypes($functionLike, [
            Return_::class,
            Yield_::class,
            // possibly unneded contract override
            Throw_::class,
            Node\Stmt\Throw_::class,
        ]);
    }

    /**
     * @param ClassMethod|Function_ $functionLike
     */
    private function hasVoidReturnType(FunctionLike $functionLike): bool
    {
        if ($functionLike->returnType === null) {
            return false;
        }

        return $this->simpleNameResolver->isName($functionLike->returnType, 'void');
    }
}
