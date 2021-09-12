<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ParentLayoutNameNodeVisitor extends NodeVisitorAbstract
{
    private string $currentFilePath = '';

    private string|null $parentLayoutFileName = null;

    public function __construct(
        private SmartFileSystem $smartFileSystem,
        private NodeValueResolver $nodeValueResolver,
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    public function setCurrentFilePath(string $currentFilePath): void
    {
        $this->currentFilePath = $currentFilePath;
    }

    public function enterNode(Node $node): null|int
    {
        if (! $node instanceof Assign) {
            return null;
        }

        $parentLayoutTemplate = $this->matchParentLayoutName($node);
        if ($parentLayoutTemplate === null) {
            return null;
        }

        // find and analyse?
        $currentFileRealPath = realpath($this->currentFilePath);
        $layoutTemplateFilePath = dirname($currentFileRealPath) . '/' . $parentLayoutTemplate;

        if (! $this->smartFileSystem->exists($layoutTemplateFilePath)) {
            return null;
        }

        $this->parentLayoutFileName = $layoutTemplateFilePath;
        return NodeTraverser::STOP_TRAVERSAL;
    }

    public function getParentLayoutFileName(): ?string
    {
        return $this->parentLayoutFileName;
    }

    private function matchParentLayoutName(Assign $assign): string|null
    {
        if (! $assign->var instanceof PropertyFetch) {
            return null;
        }

        $propertyFetch = $assign->var;
        if (! $this->simpleNameResolver->isName($propertyFetch->var, 'this')) {
            return null;
        }

        if (! $this->simpleNameResolver->isName($propertyFetch->name, 'parentName')) {
            return null;
        }

        return $this->nodeValueResolver->resolve($assign->expr, $this->currentFilePath);
    }
}
