<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Naming;

use Nette\Utils\Strings;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;

final class AssignToVariableChecker
{
    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    public function isAssignToVariableRegex(Expr $expr, string $regex): bool
    {
        $assign = $this->simpleNodeFinder->findFirstParentByType($expr, Assign::class);
        if (! $assign instanceof Assign) {
            return false;
        }

        $assignVar = $assign->var;
        while ($assignVar instanceof ArrayDimFetch) {
            $assignVar = $assignVar->var;
        }

        if (! $assignVar instanceof Variable) {
            return false;
        }

        $variableName = $this->simpleNameResolver->getName($assignVar);
        if (! is_string($variableName)) {
            return false;
        }

        $match = Strings::match($variableName, $regex);
        return $match !== null;
    }
}
