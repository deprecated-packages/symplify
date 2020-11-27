<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\If_;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ThisType;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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

        /** @var Expression $parent */
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        /** @var If_null $mayBeif */
        $mayBeif = $parent->getAttribute(PHPStanAttributeKey::PARENT);

        if (! $mayBeif instanceof If_) {
            return [];
        }

        dump('here');

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
        if (! $class instanceof Name) { dump('here');
            return [];
        }

        $currentClass = $this->resolveCurrentClass($methodCall);
        $methodCallName = $this->getMethodCallName($methodCall);

        foreach ($args as $arg) {
            if (! $this->areNodesEqual($instanceof->expr, $arg->value)) { dump('here');
                continue;
            }

            $classMethod = $currentClass->getClassMethod($methodCallName);
            if (! $classMethod instanceof ClassMethod) {
                continue;
            }

            /** @var Param[] $params */
            $params = $classMethod->getParams();
            $this->validateParam($params, $instanceof->expr);
        }

        return [];
    }

    /**
     * @return Param[] $params
     */
    private function validateParam(array $params, Expr $expr)
    {
        foreach ($params as $param) {
            $type = $param->type;
            dump($type);
        }
    }

    private function areNodesEqual(Node $firstNode, Node $secondNode): bool
    {
        return $this->printerStandard->prettyPrint([$firstNode]) === $this->printerStandard->prettyPrint([$secondNode]);
    }
}
