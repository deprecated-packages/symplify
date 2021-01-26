<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use Symplify\Astral\Naming\SimpleNameResolver;

final class VariableUsageAnalyzer
{
    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @param Assign[] $assigns
     */
    public function isUsePropertyOrCall(array $assigns): bool
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
}
