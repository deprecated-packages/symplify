<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\NetteTypeAnalyzer;

final class TemplatePathFinderVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $templatePaths = [];

    public function __construct(
        private Scope $scope,
        private SimpleNameResolver $simpleNameResolver,
        private NetteTypeAnalyzer $netteTypeAnalyzer,
        private NodeValueResolver $nodeValueResolver
    ) {
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof MethodCall) {
            return null;
        }

        $methodName = $this->simpleNameResolver->getName($node->name);
        if (! in_array($methodName, ['setFile', 'render', 'renderToString'], true)) {
            return null;
        }

        if (! $this->netteTypeAnalyzer->isTemplateType($node->var, $this->scope)) {
            return null;
        }

        $pathArg = $node->args[0] ?? null;
        if ($pathArg === null) {
            return null;
        }
        $path = $this->nodeValueResolver->resolveWithScope($pathArg->value, $this->scope);
        if ($path) {
            $this->templatePaths[] = $path;
        }

        return null;
    }

    /**
     * call after traversing
     *
     * @return string[]
     */
    public function getTemplatePaths(): array
    {
        return $this->templatePaths;
    }
}
