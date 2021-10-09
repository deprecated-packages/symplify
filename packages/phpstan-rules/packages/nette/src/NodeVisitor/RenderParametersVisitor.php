<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\NetteTypeAnalyzer;

final class RenderParametersVisitor extends NodeVisitorAbstract
{
    /** @var array<string, Expr>  */
    private array $parameters = [];

    public function __construct(
        private Scope $scope,
        private NetteTypeAnalyzer $netteTypeAnalyzer,
        private NodeValueResolver $nodeValueResolver
    ) {
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof MethodCall) {
            return null;
        }

        if (! in_array($node->name->name, ['render', 'renderToString'], true)) {
            return null;
        }

        if (! $this->netteTypeAnalyzer->isTemplateType($node->var, $this->scope)) {
            return null;
        }

        /** @var Arg $renderParameters */
        $renderParameters = $node->args[1] ?? null;
        if (! $renderParameters) {
            return null;
        }

        $parameters = $renderParameters->value;
        if (! $parameters instanceof Array_) {
            return null;
        }

        /** @var ArrayItem|null $parameter */
        foreach ($parameters->items as $parameter) {
            if (! $parameter instanceof ArrayItem) {
                continue;
            }
            if ($parameter->key === null) {
                continue;
            }

            $this->parameters[$this->nodeValueResolver->resolveWithScope($parameter->key, $this->scope)] = $parameter->value;
        }

        return null;
    }

    /**
     * call after traversing
     * @return array<string, Expr>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
