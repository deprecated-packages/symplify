<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanLatteRules\NodeAnalyzer\NetteTypeAnalyzer;

final class AssignedParametersVisitor extends NodeVisitorAbstract
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
        if (! $node instanceof Assign) {
            return null;
        }

        if ($node->var instanceof Variable) {
            $var = $node->var;
            $nameNode = $node->var->name;
        } elseif ($node->var instanceof PropertyFetch) {
            $var = $node->var->var;
            $nameNode = $node->var->name;
        } else {
            return null;
        }

        if (! $this->netteTypeAnalyzer->isTemplateType($var, $this->scope)) {
            return null;
        }

        $name = $this->simpleNameResolver->getName($nameNode);
        if (! $name) {
            return null;
        }

        $this->parameters[] = new ArrayItem($node->expr, new String_($name));
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
