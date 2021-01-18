<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\While_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;

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
    private const LOOP_STMTS = [Do_::class, For_::class, Foreach_::class, While_::class];

    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var Expr
     */
    private $assignVariable;

    public function __construct(ParentNodeFinder $parentNodeFinder, NodeFinder $nodeFinder, SimpleNameResolver $simpleNameResolver)
    {
        $this->parentNodeFinder = $parentNodeFinder;
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Assign::class];
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $loop = $this->parentNodeFinder->getFirstParentByTypes($node, self::LOOP_STMTS);
        if (! $loop instanceof Stmt) {
            return [];
        }

        if ($loop instanceof Foreach_) {
            return $this->validateForeach($node, $loop);
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @return string[]
     */
    private function validateForeach(Assign $assign, Foreach_ $foreach): array
    {
        $isInAssign = (bool) $this->nodeFinder->findFirst($assign, function (Node $node) use ($foreach): bool {
            $isExprInAssign = $foreach->expr instanceof Expr && $this->simpleNameResolver->areNamesEqual($node, $foreach->expr);
            if ($isExprInAssign) {
                return true;
            }

            $isKeyVarInAssign = $foreach->keyVar instanceof Expr && $this->simpleNameResolver->areNamesEqual($node, $foreach->keyVar);
            if ($isKeyVarInAssign) {
                return true;
            }

            return $foreach->valueVar instanceof Expr && $this->simpleNameResolver->areNamesEqual($node, $foreach->valueVar);
        });

        if ($isInAssign) {
            $this->assignVariable = $assign->var;
            return [];
        }

        $isUsingPrevAssignVariable = (bool) $this->nodeFinder->findFirst($assign, function (Node $node) : bool {
            return $this->simpleNameResolver->areNamesEqual($node, $this->assignVariable);
        });

        if ($isUsingPrevAssignVariable) {
            return [];
        }

        return [self::ERROR_MESSAGE];
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
}
