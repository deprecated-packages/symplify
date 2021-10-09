<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\NetteTypeAnalyzer;

final class AssignedParametersVisitor extends NodeVisitorAbstract
{
    /** @var array<string, Expr>  */
    private array $parameters = [];

    public function __construct(
        private Scope $scope,
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
            $name = $node->var->name;
        } elseif ($node->var instanceof PropertyFetch) {
            $var = $node->var->var;
            $name = $node->var->name->name;
        } else {
            return null;
        }

        if (! $this->netteTypeAnalyzer->isTemplateType($var, $this->scope)) {
            return null;
        }

        $this->parameters[$name] = $node->expr;
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
