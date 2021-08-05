<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\Stmt\While_;
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

    /**
     * @param Assign[] $assigns
     * @param string[] $allowedVariableNames
     * @return array<string, Assign[]>
     */
    public function resolveAssignsByVariableNames(array $assigns, array $allowedVariableNames): array
    {
        $assignsByVariableNames = [];

        foreach ($assigns as $assign) {
            if (! $assign->var instanceof Variable) {
                continue;
            }

            if ($this->shouldSkipAssign($assign)) {
                continue;
            }

            $variableName = $this->simpleNameResolver->getName($assign->var);
            if ($variableName === null) {
                continue;
            }

            if ($this->isAllowedVariableName($variableName, $allowedVariableNames)) {
                continue;
            }

            if ($this->isAssignOnSameVariable($assign, $variableName)) {
                continue;
            }

            $assignsByVariableNames['$' . $variableName][] = $assign;
        }

        return $assignsByVariableNames;
    }

    private function shouldSkipAssign(Assign $assign): bool
    {
        // skip initializations
        if ($this->isInitializationExpr($assign->expr)) {
            return true;
        }

        $parentScopeNode = $this->simpleNodeFinder->findFirstParentByTypes($assign, [
            For_::class, Foreach_::class, While_::class, If_::class, Do_::class, TryCatch::class,
        ]);

        return $parentScopeNode !== null;
    }

    private function isAssignOnSameVariable(Assign $assign, string $variableName): bool
    {
        $usedVariables = $this->simpleNodeFinder->findByType($assign->expr, Variable::class);

        foreach ($usedVariables as $usedVariable) {
            if ($this->simpleNameResolver->isName($usedVariable, $variableName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $allowedVariableNames
     */
    private function isAllowedVariableName(string $variableName, array $allowedVariableNames): bool
    {
        foreach ($allowedVariableNames as $allowedVariableName) {
            if (Strings::match($variableName, '#' . $allowedVariableName . '#i')) {
                return true;
            }
        }

        return false;
    }

    private function isInitializationExpr(Expr $expr): bool
    {
        if ($expr instanceof Array_ && $expr->items === []) {
            return true;
        }

        if ($expr instanceof ConstFetch && $this->simpleNameResolver->isNames($expr, ['true', 'false', 'null'])) {
            return true;
        }

        if ($expr instanceof String_ && $expr->value === '') {
            return true;
        }

        return $expr instanceof LNumber && $expr->value === 0;
    }
}
