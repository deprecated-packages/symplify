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
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\TypeAnalyzer\ObjectTypeAnalyzer;
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
     * @var array<class-string>
     */
    private const ALLOWED_CLASS_TYPES = [Strings::class, TrinaryLogic::class];

    public function __construct(
        private NodeFinder $nodeFinder,
        private SimpleNameResolver $simpleNameResolver,
        private ObjectTypeAnalyzer $objectTypeAnalyzer
    ) {
    }

    /**
     * @return array<class-string<Node>>
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
     * @param MethodCall|StaticCall $expr
     */
    private function shouldSkipCall(Expr $expr, Scope $scope): bool
    {
        if ($expr->args === []) {
            return true;
        }

        if ($this->isAllowedCallerType($scope, $expr)) {
            return true;
        }

        $callType = $scope->getType($expr);

        if ($this->objectTypeAnalyzer->isObjectOrUnionOfObjectTypes($callType, self::ALLOWED_CLASS_TYPES)) {
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

            return new ObjectType($className);
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

        return $this->objectTypeAnalyzer->isObjectOrUnionOfObjectTypes($type, self::ALLOWED_CLASS_TYPES);
    }

    /**
     * @return array<StaticCall|MethodCall>
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
