<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\ValueObject\AttributeKey;

final class CollectUsedVariablesNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private const INTERNAL_TWIG_VARIABLE_NAMES = ['context', 'macros', 'this', '_parent', 'loop', 'tmp'];

    /**
     * @var string[]
     */
    private array $usedVariableNames = [];

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes): array
    {
        $this->usedVariableNames = [];
        return $nodes;
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof ClassMethod) {
            return null;
        }

        if (! $this->simpleNameResolver->isName($node, 'doDisplay')) {
            return null;
        }

        $this->usedVariableNames = $this->resolveVariableNames($node);

        return null;
    }

    /**
     * @return string[]
     */
    public function getUsedVariableNames(): array
    {
        return array_diff($this->usedVariableNames, self::INTERNAL_TWIG_VARIABLE_NAMES);
    }

    /**
     * @return string[]
     */
    private function resolveVariableNames(ClassMethod $classMethod): array
    {
        $variableNames = [];
        $justCreatedVariableNames = [];

        /** @var Variable[] $variables */
        $variables = $this->nodeFinder->findInstanceOf((array) $classMethod->stmts, Variable::class);
        foreach ($variables as $variable) {
            if ($this->isJustCreatedVariable($variable)) {
                $justCreatedVariableNames[] = $this->simpleNameResolver->getName($variable);
                continue;
            }

            $variableName = $this->simpleNameResolver->getName($variable);
            if ($variableName === null) {
                continue;
            }

            $variableNames[] = $variableName;
        }

        return array_diff($variableNames, $justCreatedVariableNames);
    }

    private function isJustCreatedVariable(Variable $variable): bool
    {
        $parent = $variable->getAttribute(AttributeKey::PARENT);
        if ($parent instanceof Assign && $parent->var === $variable) {
            return true;
        }
        if (! $parent instanceof Foreach_) {
            return false;
        }
        if ($parent->valueVar !== $variable) {
            return false;
        }
        return true;
    }
}
