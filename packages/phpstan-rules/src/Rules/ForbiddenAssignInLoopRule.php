<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
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
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var PreviouslyUsedAnalyzer
     */
    private $previouslyUsedAnalyzer;

    public function __construct(NodeFinder $nodeFinder, PreviouslyUsedAnalyzer $previouslyUsedAnalyzer)
    {
        $this->nodeFinder = $nodeFinder;
        $this->previouslyUsedAnalyzer = $previouslyUsedAnalyzer;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Do_::class, For_::class, Foreach_::class, While_::class];
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
     * @param Expr[]|null|Expr $expr
     * @return string[]
     */
    private function validateVarExprAssign(array $assigns, Node $node, $expr): array
    {
        if ($expr === null) {
            return [self::ERROR_MESSAGE];
        }

        /** @var Variable[] $variables */
        $variables = $this->nodeFinder->findInstanceOf($expr, Variable::class);
        if ($this->previouslyUsedAnalyzer->isInAssignOrUsedPreviously($assigns, $variables, $node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
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
