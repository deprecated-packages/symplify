<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PHPStanRules\NodeFinder\PreviousLoopFinder;

final class PreviouslyUsedAnalyzer
{
    /**
     * @var array<class-string<Stmt>>
     */
    private const IF_AND_LOOP_NODE_TYPES = [If_::class, Do_::class, For_::class, Foreach_::class, While_::class];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var PreviousLoopFinder
     */
    private $previousLoopFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var SimpleNodeFinder
     */
    private $simpleNodeFinder;

    public function __construct(
        NodeFinder $nodeFinder,
        PreviousLoopFinder $previousLoopFinder,
        SimpleNameResolver $simpleNameResolver,
        SimpleNodeFinder $simpleNodeFinder
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->previousLoopFinder = $previousLoopFinder;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->simpleNodeFinder = $simpleNodeFinder;
    }

    /**
     * @param Assign[] $assigns
     * @param Variable[] $variables
     */
    public function isInAssignOrUsedPreviously(array $assigns, array $variables, Node $node): bool
    {
        foreach ($assigns as $assign) {
            if ($this->isInsideIf($assign)) {
                return true;
            }

            if ($this->isInAssign($variables, $assign)) {
                return true;
            }

            /** @var Variable[] $variablesInAssign */
            $variablesInAssign = $this->nodeFinder->findInstanceOf($assign, Variable::class);
            if ($this->previousLoopFinder->isUsedInPreviousLoop($variablesInAssign, $node)) {
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
            $isInAssign = (bool) $this->nodeFinder->findFirst($assign, function (Node $node) use ($variable): bool {
                return $this->simpleNameResolver->areNamesEqual($node, $variable);
            });
            if ($isInAssign) {
                return true;
            }
        }

        return false;
    }

    private function isInsideIf(Assign $assign): bool
    {
        $previousLoopOrIf = $this->simpleNodeFinder->findFirstParentByTypes($assign, self::IF_AND_LOOP_NODE_TYPES);
        return $previousLoopOrIf instanceof If_;
    }
}
