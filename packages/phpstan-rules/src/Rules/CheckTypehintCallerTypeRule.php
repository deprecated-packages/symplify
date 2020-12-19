<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ThisType;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Printer\NodeComparator;
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
    public const ERROR_MESSAGE = 'Parameter %d should use "%s" type as the only type passed to this method';

    /**
     * @var NodeFinder
     */

    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var NodeComparator
     */
    private $nodeComparator;

    public function __construct(
        NodeComparator $nodeComparator,
        NodeFinder $nodeFinder,
        SimpleNameResolver $simpleNameResolver
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->nodeComparator = $nodeComparator;
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

        /** @var Expression|null $parent */
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $parent instanceof Node) {
            return [];
        }

        /** @var If_|null $mayBeif */
        $mayBeif = $parent->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $mayBeif instanceof If_) {
            return [];
        }

        // ensure check prev expression that may override
        $previous = $parent->getAttribute(PHPStanAttributeKey::PREVIOUS);
        if (! $previous instanceof Instanceof_) {
            return [];
        }

        $args = $node->args;
        if ($args === []) {
            return [];
        }

        $cond = $mayBeif->cond;
        if ($cond instanceof Instanceof_ && $cond->class instanceof FullyQualified) {
            return $this->validateInstanceOf($cond->expr, $cond->class, $args, $node);
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
    private function validateInstanceOf(Expr $expr, FullyQualified $class, array $args, MethodCall $methodCall)
    {
        /** @var Class_|null $currentClass */
        $currentClass = $this->resolveCurrentClass($methodCall);
        if (! $currentClass instanceof Class_) {
            return [];
        }

        $methodCallUses = $this->findMethodCallUses($currentClass, $methodCall);
        if (count($methodCallUses) > 1) {
            return [];
        }

        /** @var string|null $methodCallName */
        $methodCallName = $this->simpleNameResolver->getName($methodCall->name);
        if ($methodCallName === null) {
            return [];
        }

        /** @var ClassMethod|null $classMethod */
        $classMethod = $currentClass->getMethod($methodCallName);
        if (! $classMethod instanceof ClassMethod || ! $classMethod->isPrivate()) {
            return [];
        }

        /** @var Param[] $params */
        $params = $classMethod->getParams();

        foreach ($args as $position => $arg) {
            if (! $this->nodeComparator->areNodesEqual($expr, $arg->value)) {
                continue;
            }

            $validateParam = $this->validateParam($params, $position, $class);
            if ($validateParam === []) {
                continue;
            }

            return $validateParam;
        }

        return [];
    }

    /**
     * @return Param[] $params
     * @return string[]
     */
    private function validateParam(array $params, int $position, FullyQualified $class): array
    {
        foreach ($params as $i => $param) {
            if ($i !== $position) {
                continue;
            }

            $type = $param->type;
            if (! $type instanceof FullyQualified) {
                continue;
            }

            if ($this->nodeComparator->areNodesEqual($class, $type)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $i + 1, $class->toString());
            return [$errorMessage];
        }

        return [];
    }

    /**
     * @return MethodCall[]
     */
    private function findMethodCallUses(Class_ $class, MethodCall $methodCall): array
    {
        return $this->nodeFinder->find($class, function (Node $node) use ($methodCall): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if (! $this->nodeComparator->areNodesEqual($node->var, $methodCall->var)) {
                return false;
            }

            return $this->nodeComparator->areNodesEqual($node->name, $methodCall->name);
        });
    }
}
