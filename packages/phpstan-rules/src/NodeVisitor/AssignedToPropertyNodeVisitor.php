<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\Enum\AttributeKey;

/**
 * Inspired by https://github.com/phpstan/phpstan-src/blob/1.7.x/src/Parser/NewAssignedToPropertyVisitor.php
 */
final class AssignedToPropertyNodeVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node): ?Node
    {
        if (! $node instanceof Assign) {
            return null;
        }

        $node->expr->setAttribute(AttributeKey::ASSIGNED_TO, $node->var);
        return null;
    }
}
