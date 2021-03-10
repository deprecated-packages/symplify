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
    /**
     * @var SimpleNodeFinder
     */
    private $simpleNodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNodeFinder $simpleNodeFinder, SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNodeFinder = $simpleNodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
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
