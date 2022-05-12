<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
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
        private SimpleNameResolver $simpleNameResolver,
        private InitializedExprAnalyzer $initializedExprAnalyzer
    ) {
    }

    public function isVariableNameBeingAssigned(ClassMethod $classMethod, string $variableName): bool
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
     * @return array<string, Assign[]>
     */
    public function resolveExclusivelyNewAssignsByVariableNames(array $assigns): array
    {
        $assignsByVariableNames = $this->resolveAssignsByVariableNames($assigns, []);

        $exclusiveNewAssignsByVariableNames = [];
        foreach ($assignsByVariableNames as $variableName => $assigns) {
            $exclusivelyNewAssigns = array_filter($assigns, fn (Assign $assign): bool => $assign->expr instanceof New_);
            if ($exclusivelyNewAssigns === []) {
                continue;
            }

            $exclusiveNewAssignsByVariableNames[$variableName] = $exclusivelyNewAssigns;
        }

        return $exclusiveNewAssignsByVariableNames;
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
        if ($this->initializedExprAnalyzer->isInitializationExpr($assign->expr)) {
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
}
