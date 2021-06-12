<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\PackageBuilder\Php\TypeChecker;
use Symplify\PHPStanRules\NodeAnalyzer\PreviouslyUsedAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\VariableUsageAnalyzer;
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
     * @var array<class-string<Stmt>>
     */
    private const LOOP_NODE_TYPES = [Do_::class, For_::class, Foreach_::class, While_::class];

    public function __construct(
        private NodeFinder $nodeFinder,
        private PreviouslyUsedAnalyzer $previouslyUsedAnalyzer,
        private VariableUsageAnalyzer $variableUsageAnalyzer,
        private TypeChecker $typeChecker
    ) {
    }

    /**
     * @return array<class-string<Node>>
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
        if ($this->variableUsageAnalyzer->isUsePropertyOrCall($assigns)) {
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
     * @param Do_|For_|Foreach_|While_ $node
     * @return string[]
     */
    private function revalidateExprAssignInsideLoop(array $assigns, Node $node): array
    {
        $loop = $this->nodeFinder->findFirst(
            $node->stmts,
            fn (Node $node): bool => $this->typeChecker->isInstanceOf($node, self::LOOP_NODE_TYPES)
        );

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
            return $this->validateVarExprAssign($assigns, $node, $node->cond);
        }

        if ($node instanceof For_) {
            return $this->validateAssignInFor($assigns, $node);
        }

        if ($node instanceof Foreach_) {
            return $this->validateAssignInForeach($assigns, $node);
        }

        return $this->validateVarExprAssign($assigns, $node, $node->cond);
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
}
