<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\TrinaryLogic;
use PHPStan\Type\BooleanType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\TypeAnalyzer\ObjectTypeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenComplexForeachIfExprRule\ForbiddenComplexForeachIfExprRuleTest
 */
final class ForbiddenComplexForeachIfExprRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'foreach(...), while(), for() or if(...) cannot contains a complex expression. Extract it to a new variable assign on line before';

    /**
     * @var array<class-string<Expr>>
     */
    private const CALL_CLASS_TYPES = [MethodCall::class, StaticCall::class];

    /**
     * @var array<class-string>
     */
    private const ALLOWED_CLASS_TYPES = [Strings::class, TrinaryLogic::class];

    public function __construct(
        private NodeFinder $nodeFinder,
        private SimpleNameResolver $simpleNameResolver,
        private ObjectTypeAnalyzer $objectTypeAnalyzer,
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Foreach_::class, If_::class, ElseIf_::class, For_::class, Do_::class, While_::class];
    }

    /**
     * @param Foreach_|If_|ElseIf_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $expr = $node instanceof Foreach_ ? $node->expr : $node->cond;

        $assigns = $this->nodeFinder->findInstanceOf($expr, Assign::class);
        if ($assigns !== []) {
            return [self::ERROR_MESSAGE];
        }

        foreach (self::CALL_CLASS_TYPES as $expressionClassType) {
            /** @var MethodCall[]|StaticCall[] $calls */
            $calls = $this->nodeFinder->findInstanceOf($expr, $expressionClassType);
            foreach ($calls as $call) {
                if ($this->shouldSkipCall($call, $scope)) {
                    continue;
                }

                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
foreach ($this->getData($arg) as $key => $item) {
    // ...
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$data = $this->getData($arg);
foreach ($arg as $key => $item) {
    // ...
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipCall(MethodCall | StaticCall $expr, Scope $scope): bool
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

    private function resolveCalleeType(Scope $scope, StaticCall | MethodCall $node): Type
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

    private function isAllowedCallerType(Scope $scope, StaticCall | MethodCall $node): bool
    {
        if (! $node instanceof StaticCall) {
            return false;
        }

        $callerType = $this->resolveCalleeType($scope, $node);

        return $this->objectTypeAnalyzer->isObjectOrUnionOfObjectTypes($callerType, self::ALLOWED_CLASS_TYPES);
    }
}
