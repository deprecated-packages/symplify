<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\PreviouslyUsedAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\ForbiddenAssignInLoopRuleTest
 */
final class ForbiddenAssignInLoopRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Assign in loop is not allowed.';

    /**
     * @var string[]
     */
    private const LOOP_NODE_TYPES = [Do_::class, For_::class, Foreach_::class, While_::class];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var PreviouslyUsedAnalyzer
     */
    private $previouslyUsedAnalyzer;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(
        NodeFinder $nodeFinder,
        PreviouslyUsedAnalyzer $previouslyUsedAnalyzer,
        SimpleNameResolver $simpleNameResolver
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->previouslyUsedAnalyzer = $previouslyUsedAnalyzer;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return self::LOOP_NODE_TYPES;
    }

    /**
     * @param Do_|For_|Foreach_|While_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Assign[] $assigns */
        $assigns = $this->nodeFinder->findInstanceOf($node->stmts, Assign::class);
        if ($assigns === []) {
            return [];
        }

        return $this->validateAssignInLoop($assigns, $node);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
foreach (...) {
    $value = new SmartFileInfo('a.php');
    if ($value) {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$value = new SmartFileInfo('a.php');
foreach (...) {
    if ($value) {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param Assign[] $assigns
     * @param Do_|For_|Foreach_|While_ $node
     * @param Expr[]|null|Expr $expr
     * @return string[]
     */
    private function validateVarExprAssign(array $assigns, Node $node, $expr): array
    {
        if ($this->isUsePropertyOrCall($assigns)) {
            return [];
        }

        if ($expr === null) {
            return [self::ERROR_MESSAGE];
        }

        /** @var Variable[] $variables */
        $variables = $this->nodeFinder->findInstanceOf($expr, Variable::class);
        if ($this->previouslyUsedAnalyzer->isInAssignOrUsedPreviously($assigns, $variables, $node)) {
            return [];
        }

        return $this->revalidateExprAssignInsideLoop($assigns, $node);
    }

    /**
     * @param Assign[] $assigns
     */
    private function isUsePropertyOrCall(array $assigns): bool
    {
        foreach ($assigns as $assign) {
            if (! $assign->var instanceof Variable) {
                return true;
            }

            if ($assign->expr instanceof PropertyFetch) {
                return true;
            }

            if ($assign->expr instanceof StaticPropertyFetch) {
                return true;
            }

            if (! $assign->expr instanceof MethodCall && ! $assign->expr instanceof StaticCall) {
                continue;
            }

            if ($this->isArgPropertyOrAssignVariable($assign->expr->args, $assign->var)) {
                return true;
            }
        }

        return false;
    }

    private function isArgPropertyOrAssignVariable(array $args, Variable $variable): bool
    {
        foreach ($args as $arg) {
            if ($arg->value instanceof PropertyFetch) {
                return true;
            }

            if ($arg->value instanceof StaticPropertyFetch) {
                return true;
            }

            if ($this->simpleNameResolver->areNamesEqual($arg->value, $variable)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Assign[] $assigns
     * @param Do_|For_|Foreach_|While_ $node
     * @return string[]
     */
    private function revalidateExprAssignInsideLoop(array $assigns, Node $node): array
    {
        $loop = $this->nodeFinder->findFirst($node->stmts, function (Node $n): bool {
            foreach (self::LOOP_NODE_TYPES as $loopType) {
                if (! is_a($n, $loopType, true)) {
                    continue;
                }

                return true;
            }

            return false;
        });

        if (! $loop instanceof Node) {
            return [self::ERROR_MESSAGE];
        }

        /** @var Do_|For_|Foreach_|While_ $loop */
        return $this->validateAssignInLoop($assigns, $loop);
    }

    /**
     * @param Assign[] $assigns
     * @param Do_|For_|Foreach_|While_ $node
     * @return string[]
     */
    private function validateAssignInLoop(array $assigns, Node $node): array
    {
        if ($node instanceof Do_) {
            return $this->validateAssignInDo($assigns, $node);
        }

        if ($node instanceof For_) {
            return $this->validateAssignInFor($assigns, $node);
        }

        if ($node instanceof Foreach_) {
            return $this->validateAssignInForeach($assigns, $node);
        }

        return $this->validateAssignInWhile($assigns, $node);
    }

    /**
     * @param Assign[] $assigns
     * @return string[]
     */
    private function validateAssignInDo(array $assigns, Do_ $do): array
    {
        return $this->validateVarExprAssign($assigns, $do, $do->cond);
    }

    /**
     * @param Assign[] $assigns
     * @return string[]
     */
    private function validateAssignInFor(array $assigns, For_ $for): array
    {
        $validateInit = $this->validateVarExprAssign($assigns, $for, $for->init);
        if ($validateInit === []) {
            return [];
        }

        $validateCond = $this->validateVarExprAssign($assigns, $for, $for->cond);
        if ($validateCond === []) {
            return [];
        }

        return $this->validateVarExprAssign($assigns, $for, $for->loop);
    }

    /**
     * @param Assign[] $assigns
     * @return string[]
     */
    private function validateAssignInForeach(array $assigns, Foreach_ $foreach): array
    {
        $validateExpr = $this->validateVarExprAssign($assigns, $foreach, $foreach->expr);
        if ($validateExpr === []) {
            return [];
        }

        $validateKeyVar = $this->validateVarExprAssign($assigns, $foreach, $foreach->keyVar);
        if ($validateKeyVar === []) {
            return [];
        }

        return $this->validateVarExprAssign($assigns, $foreach, $foreach->valueVar);
    }

    /**
     * @param Assign[] $assigns
     * @return string[]
     */
    private function validateAssignInWhile(array $assigns, While_ $while): array
    {
        return $this->validateVarExprAssign($assigns, $while, $while->cond);
    }
}
