<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanLatteRules\NodeAnalyzer\NetteTypeAnalyzer;

final class RenderParametersVisitor extends NodeVisitorAbstract
{
    /**
     * @var ArrayItem[]
     */
    private array $parameters = [];

    public function __construct(
        private Scope $scope,
        private SimpleNameResolver $simpleNameResolver,
        private NetteTypeAnalyzer $netteTypeAnalyzer
    ) {
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof MethodCall) {
            return null;
        }

        $methodName = $this->simpleNameResolver->getName($node->name);
        if (! in_array($methodName, ['render', 'renderToString'], true)) {
            return null;
        }

        if (! $this->netteTypeAnalyzer->isTemplateType($node->var, $this->scope)) {
            return null;
        }

        $renderParameters = $node->args[1] ?? null;
        if (! $renderParameters instanceof Arg) {
            return null;
        }

        $parameters = $renderParameters->value;
        if (! $parameters instanceof Array_) {
            return null;
        }

        $this->parameters = array_filter($parameters->items);
        return null;
    }

    /**
     * call after traversing
     *
     * @return ArrayItem[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
