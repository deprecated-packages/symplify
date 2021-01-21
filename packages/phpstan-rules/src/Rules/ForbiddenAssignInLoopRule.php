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
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
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
        if ($this->isInAssignOrUsedPreviously($assigns, $variables, $node)) {
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

    /**
     * @param Assign[] $assigns
     * @param Variable[] $variables
     */
    private function isInAssignOrUsedPreviously(array $assigns, array $variables, Node $node): bool
    {
        foreach ($assigns as $assign) {
            if ($this->isInAssign($variables, $assign)) {
                return true;
            }

            /** @var Variable[] $variablesInAssign */
            $variablesInAssign = $this->nodeFinder->findInstanceOf($assign, Variable::class);
            if ($this->isUsedInPreviousLoop($variablesInAssign, $node)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Variable[] $variables
     */
    private function isInAssign(array $variables, Assign $assign): bool
    {
        foreach ($variables as $variable) {
            $isInAssign = (bool) $this->nodeFinder->findFirst($assign, function (Node $n) use ($variable): bool {
                return $this->simpleNameResolver->areNamesEqual($n, $variable);
            });
            if ($isInAssign) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Variable[] $variables
     */
    private function isUsedInPreviousLoop(array $variables, Node $node): bool
    {
        $previous = $node->getAttribute(PHPStanAttributeKey::PREVIOUS);
        if (! $previous instanceof Node) {
            $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
            if (! $parent instanceof Node) {
                return false;
            }

            return $this->isUsedInPreviousLoop($variables, $parent);
        }

        foreach ($variables as $variable) {
            $isInPrevious = (bool) $this->nodeFinder->findFirst($previous, function (Node $n) use ($variable): bool {
                return $this->simpleNameResolver->areNamesEqual($n, $variable);
            });
            if ($isInPrevious) {
                return true;
            }
        }

        return false;
    }
}
