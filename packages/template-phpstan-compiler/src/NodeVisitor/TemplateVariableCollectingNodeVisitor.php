<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\NodeVisitor;

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

/**
 * @api
 */
final class TemplateVariableCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $userVariableNames = [];

    /**
     * @var string[]
     */
    private array $justCreatedVariableNames = [];

    /**
     * @param array<string> $defaultVariableNames
     * @param array<string> $renderMethodNames
     */
    public function __construct(
        private array $defaultVariableNames,
        private array $renderMethodNames,
        private SimpleNameResolver $simpleNameResolver,
        private NodeFinder $nodeFinder,
    ) {
    }

    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes): array
    {
        // reset to avoid used variable name in next analysed file
        $this->userVariableNames = [];
        $this->justCreatedVariableNames = [];

        return $nodes;
    }

    public function enterNode(Node $node): Node|null
    {
        if (! $node instanceof ClassMethod) {
            return null;
        }

        if (! $this->simpleNameResolver->isNames($node, $this->renderMethodNames)) {
            return null;
        }

        $this->userVariableNames = array_merge($this->userVariableNames, $this->resolveClassMethodVariableNames($node));
        return null;
    }

    /**
     * @return string[]
     */
    public function getUsedVariableNames(): array
    {
        $removedVariableNames = array_merge($this->defaultVariableNames, $this->justCreatedVariableNames);

        return array_diff($this->userVariableNames, $removedVariableNames);
    }

    /**
     * @return string[]
     */
    private function resolveClassMethodVariableNames(ClassMethod $classMethod): array
    {
        $variableNames = [];

        /** @var Variable[] $variables */
        $variables = $this->nodeFinder->findInstanceOf((array) $classMethod->stmts, Variable::class);

        foreach ($variables as $variable) {
            $variableName = $this->simpleNameResolver->getName($variable);
            if ($variableName === null) {
                continue;
            }

            if ($this->isJustCreatedVariable($variable)) {
                $this->justCreatedVariableNames[] = $variableName;
                continue;
            }

            $variableNames[] = $variableName;
        }

        return $variableNames;
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

        return $parent->valueVar === $variable;
    }
}
