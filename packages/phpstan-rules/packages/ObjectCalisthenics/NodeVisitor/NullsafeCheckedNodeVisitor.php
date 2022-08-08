<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\Enum\AttributeKey;

final class NullsafeCheckedNodeVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        if (! $node instanceof NullsafeMethodCall) {
            return null;
        }

        $node->var->setAttribute(AttributeKey::NULLSAFE_CHECKED, true);
        return null;
    }
}
