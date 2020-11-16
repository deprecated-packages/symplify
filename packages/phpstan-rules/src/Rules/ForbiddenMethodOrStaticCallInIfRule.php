<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\TrinaryLogic;
use PHPStan\Type\BooleanType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Rector\PHPStan\Type\FullyQualifiedObjectType;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule\ForbiddenMethodOrStaticCallInIfRuleTest
 */
final class ForbiddenMethodOrStaticCallInIfRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method nor static call in if() or elseif() is not allowed. Extract expression to a new variable assign on line before';

    /**
     * @var string[]
     */
    private const ALLOWED_CLASS_TYPES = [Strings::class, TrinaryLogic::class];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(NodeFinder $nodeFinder, SimpleNameResolver $simpleNameResolver)
    {
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [If_::class, ElseIf_::class];
    }

    /**
     * @param If_|ElseIf_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var MethodCall[]|StaticCall[] $calls */
        $calls = $this->findCallsInIfCond($node->cond);

        foreach ($calls as $call) {
            if ($this->shouldSkipCall($call, $scope)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$someObject = new SomeClass();
if ($someObject->getData($arg) === []) {
    // ...
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$someObject = new SomeClass();
$dataFirstArg = $someObject->getData($arg);
if ($dataFirstArg === []) {
    // ...
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param MethodCall|StaticCall $call
     */
    private function shouldSkipCall(Expr $call, Scope $scope): bool
    {
        if ($call->args === []) {
            return true;
        }

        if ($this->isAllowedCallerType($scope, $call)) {
            return true;
        }

        $callType = $scope->getType($call);

        if ($this->isAllowedClassType($callType)) {
            return true;
        }

        return $callType instanceof BooleanType;
    }

    /**
     * @param StaticCall|MethodCall $node
     */
    private function resolveCalleeType(Scope $scope, Node $node): Type
    {
        if ($node instanceof StaticCall) {
            $className = $this->simpleNameResolver->getName($node->class);
            if ($className === null) {
                return new MixedType();
            }

            return new FullyQualifiedObjectType($className);
        }

        return $scope->getType($node->var);
    }

    /**
     * @param StaticCall|MethodCall $node
     */
    private function isAllowedCallerType(Scope $scope, Node $node): bool
    {
        $type = $this->resolveCalleeType($scope, $node);
        if ($type instanceof ThisType) {
            return true;
        }

        if (! $node instanceof StaticCall) {
            return false;
        }

        if ($this->isAllowedClassType($type)) {
            return true;
        }

        return false;
    }

    private function isAllowedClassType(Type $type): bool
    {
        if (! $type instanceof TypeWithClassName) {
            return false;
        }

        foreach (self::ALLOWED_CLASS_TYPES as $allowedClassType) {
            if (! is_a($type->getClassName(), $allowedClassType, true)) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @return StaticCall[]|MethodCall[]
     */
    private function findCallsInIfCond(Expr $expr): array
    {
        /** @var StaticCall[] $staticCalls */
        $staticCalls = $this->nodeFinder->findInstanceOf($expr, StaticCall::class);

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($expr, MethodCall::class);

        return array_merge($staticCalls, $methodCalls);
    }
}
