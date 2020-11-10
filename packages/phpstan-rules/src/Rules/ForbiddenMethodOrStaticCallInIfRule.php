<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Type\BooleanType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
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
    public const ERROR_MESSAGE = 'Method nor static call in if () or elseif () is not allowed. Extract expression to a new variable assign on line before';

    /**
     * @var string[]
     */
    private const CALL_CLASS_TYPES = [MethodCall::class, StaticCall::class];

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
        foreach (self::CALL_CLASS_TYPES as $callClassType) {
            /** @var MethodCall[]|StaticCall[] $calls */
            $calls = $this->nodeFinder->findInstanceOf($node->cond, $callClassType);
            if (! $this->hasCallArgs($calls, $scope)) {
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

} elseif ($someObject->getData($arg2) !== []) {

}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$someObject = new SomeClass();
$dataFirstArg = $someObject->getData($arg);
$dataSecondArg = $someObject->getData($arg2);

if ($dataFirstArg === []) {

} elseif ($dataSecondArg !== []) {

}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param MethodCall[]|StaticCall[] $calls
     */
    private function hasCallArgs(array $calls, Scope $scope): bool
    {
        foreach ($calls as $call) {
            if ($call->args === []) {
                continue;
            }

            $type = $this->resolveCalleeType($scope, $call);
            if ($type instanceof ThisType) {
                continue;
            }

            $callType = $scope->getType($call);
            if ($callType instanceof BooleanType) {
                continue;
            }

            return true;
        }

        return false;
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
}
