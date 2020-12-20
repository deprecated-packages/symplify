<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Naming\NameNodeResolver;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Symplify\PHPStanRules\Contract\NameNodeResolver\NameNodeResolverInterface;

final class IdentifierNameNodeResolver implements NameNodeResolverInterface
{
    public function match(Node $node): bool
    {
        if ($node instanceof Identifier) {
            return true;
        }

        return $node instanceof Name;
    }

    /**
     * @param Identifier|Name $node
     */
    public function resolve(Node $node): ?string
    {
        return (string) $node;
    }
}
