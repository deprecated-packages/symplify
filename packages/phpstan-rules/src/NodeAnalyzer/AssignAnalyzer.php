<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;

final class AssignAnalyzer
{
    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function isVarialeNameBeingAssigned(ClassMethod $classMethod, string $variableName): bool
    {
        /** @var Assign[] $assigns */
        $assigns = $this->simpleNodeFinder->findByType($classMethod, Assign::class);

        foreach ($assigns as $assign) {
            if (! $assign->var instanceof Variable) {
                continue;
            }

            if ($this->simpleNameResolver->isName($assign->var, $variableName)) {
                return true;
            }
        }

        return false;
    }
}
