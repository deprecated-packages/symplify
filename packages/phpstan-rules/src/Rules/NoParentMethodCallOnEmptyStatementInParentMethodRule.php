<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Nop;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ParentClassMethodNodeResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\NoParentMethodCallOnEmptyStatementInParentMethodRuleTest
 */
final class NoParentMethodCallOnEmptyStatementInParentMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not call parent method if parent method is empty';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ParentClassMethodNodeResolver
     */
    private $parentClassMethodNodeResolver;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        ParentClassMethodNodeResolver $parentClassMethodNodeResolver
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->parentClassMethodNodeResolver = $parentClassMethodNodeResolver;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param StaticCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->simpleNameResolver->isName($node->class, 'parent')) {
            return [];
        }

        if ($this->shouldSkipClass($scope)) {
            return [];
        }

        $methodName = $this->simpleNameResolver->getName($node->name);
        if ($methodName === null) {
            return [];
        }

        if (! $this->isParentClassMethodEmpty($scope, $methodName)) {
            return [];
        }
        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class ParentClass
{
    public function someMethod()
    {
    }
}

class SomeClass extends ParentClass
{
    public function someMethod()
    {
        parent::someMethod();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class ParentClass
{
    public function someMethod()
    {
    }
}

class SomeClass extends ParentClass
{
    public function someMethod()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isParentClassMethodEmpty(Scope $scope, string $methodName): bool
    {
        $countStmts = $this->resolveParentClassMethodStmtCount($scope, $methodName);
        if ($countStmts !== 0) {
            return false;
        }

        $parentClassMethod = $this->parentClassMethodNodeResolver->resolveParentClassMethod($scope, $methodName);
        if (! $parentClassMethod instanceof ClassMethod) {
            return true;
        }

        foreach ($parentClassMethod->getParams() as $param) {
            if ($param->flags !== null) {
                return false;
            }
        }

        return true;
    }

    private function shouldSkipClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        // skip exceptions
        return is_a($classReflection->getName(), Throwable::class, true);
    }

    private function resolveParentClassMethodStmtCount(Scope $scope, string $methodName): int
    {
        $parentClassMethod = $this->parentClassMethodNodeResolver->resolveParentClassMethod($scope, $methodName);
        if (! $parentClassMethod instanceof ClassMethod) {
            return 0;
        }

        $stmts = (array) $parentClassMethod->getStmts();

        $countStmts = 0;
        foreach ($stmts as $stmt) {
            // ensure empty statement not counted
            if ($stmt instanceof Nop) {
                continue;
            }
            ++$countStmts;
        }
        return $countStmts;
    }
}
