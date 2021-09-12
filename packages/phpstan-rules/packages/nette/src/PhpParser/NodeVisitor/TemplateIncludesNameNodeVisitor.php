<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\SmartFileSystem\SmartFileSystem;

final class TemplateIncludesNameNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $includedTemplateFilePaths = [];

    private string $templateFilePath = '';

    public function setTemplateFilePath(string $templateFilePath): void
    {
        $this->templateFilePath = $templateFilePath;
    }

    public function __construct(
        private SmartFileSystem $smartFileSystem,
        private NodeValueResolver $nodeValueResolver,
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    public function enterNode(Node $node): null|int
    {
        // match $this->createTemplate('anything.latte')
        if (! $node instanceof MethodCall) {
            return null;
        }

        $parentLayoutTemplate = $this->matchIncludedTemplateName($node);
        if ($parentLayoutTemplate === null) {
            return null;
        }

        // find and analyse?
        $currentFileRealPath = realpath($this->templateFilePath);
        $layoutTemplateFilePath = dirname($currentFileRealPath) . '/' . $parentLayoutTemplate;

        if (! $this->smartFileSystem->exists($layoutTemplateFilePath)) {
            return null;
        }

        $this->parentLayoutFileName = $layoutTemplateFilePath;
        return NodeTraverser::STOP_TRAVERSAL;
    }

    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes): array
    {
        // reset to avoid keeping old variables for new template
        $this->includedTemplateFilePaths = [];
        return $nodes;
    }

    /**
     * @return string[]
     */
    public function getIncludedTemplateFilePaths(): array
    {
        return $this->includedTemplateFilePaths;
    }

    private function matchIncludedTemplateName(MethodCall $methodCall): string|null
    {
        if (! $this->simpleNameResolver->isName($methodCall->var, 'this')) {
            return null;
        }

        if (! $this->simpleNameResolver->isName($methodCall->name, 'createTemplate')) {
            return null;
        }

        $firstArgValue = $methodCall->args[0]->value;
        return $this->nodeValueResolver->resolve($firstArgValue, $this->templateFilePath);
    }
}
