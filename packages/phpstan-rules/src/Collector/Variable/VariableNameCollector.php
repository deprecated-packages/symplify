<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\Variable;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * @implements Collector<Variable, array<string, array{string, int}>|null>
 */
final class VariableNameCollector implements Collector
{
    public function getNodeType(): string
    {
        return Variable::class;
    }

    /**
     * @param Variable $node
     * @return array{string, int}|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if ($node->name instanceof Expr) {
            return null;
        }

        return [$node->name, $node->getLine()];
    }
}
