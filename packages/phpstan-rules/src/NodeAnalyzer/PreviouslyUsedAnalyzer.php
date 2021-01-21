<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeFinder\PreviousLoopFinder;

final class PreviouslyUsedAnalyzer
{
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

    public function __construct(
        NodeFinder $nodeFinder,
        PreviousLoopFinder $previousLoopFinder,
        SimpleNameResolver $simpleNameResolver
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->previousLoopFinder = $previousLoopFinder;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @param Assign[] $assigns
     * @param Variable[] $variables
     */
    public function isInAssignOrUsedPreviously(array $assigns, array $variables, Node $node): bool
    {
        foreach ($assigns as $assign) {
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
            $isInAssign = (bool) $this->nodeFinder->findFirst($assign, function (Node $n) use ($variable): bool {
                return $this->simpleNameResolver->areNamesEqual($n, $variable);
            });
            if ($isInAssign) {
                return true;
            }
        }

        return false;
    }
}
