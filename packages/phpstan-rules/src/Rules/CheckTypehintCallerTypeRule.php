<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ThisType;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use PhpParser\Node\Name;
use PhpParser\Node\Arg;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\CheckTypehintCallerTypeRuleTest
 */
final class CheckTypehintCallerTypeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameter %d should use %s type as already checked';

    /**
     * @var Standard
     */
    private $printerStandard;

    public function __construct(Standard $printerStandard)
    {
        $this->printerStandard = $printerStandard;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $type = $scope->getType($node->var);
        if (! $type instanceof ThisType) {
            return [];
        }

        /** @var Node|null $parent */
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $parent instanceof If_) {
            return [];
        }

        $args = $node->args;
        if ($args === []) {
            return [];
        }

        $cond = $parent->cond;

        if ($cond instanceof Instanceof_) {
            return $this->validateInstanceOf($cond, $args, $node);
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

class SomeClass
{
    public function run(Node $node)
    {
        if ($node instanceof MethodCall) {
            $this->isCheck($node);
        }
    }

    private function isCheck(Node $node)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

class SomeClass
{
    public function run(Node $node)
    {
        if ($node instanceof MethodCall) {
            $this->isCheck($node);
        }
    }

    private function isCheck(MethodCall $node)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param Arg[] $args
     */
    private function validateInstanceOf(Instanceof_ $instanceof, array $args, MethodCall $methodCall)
    {
        $class = $instanceof->class;
        if (! $class instanceof Name) {
            return [];
        }

        $currentClass = $this->resolveCurrentClass($methodCall);
        $methodCallName = $this->getMethodCallName($methodCall);

        foreach ($args as $arg) {
            if (! $this->areNodesEqual($instanceof->expr, $arg->value)) {
                continue;
            }

            $classMethod = $currentClass->getClassMethod($methodCallName);
            if (! $classMethod instanceof ClassMethod) {
                continue;
            }
        }

        return [];
    }

    private function areNodesEqual(Node $firstNode, Node $secondNode): bool
    {
        return $this->printerStandard->prettyPrint([$firstNode]) === $this->printerStandard->prettyPrint([$secondNode]);
    }
}
