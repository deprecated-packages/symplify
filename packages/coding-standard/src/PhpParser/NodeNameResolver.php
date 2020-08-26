<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

final class NodeNameResolver
{
    /**
     * @param string|Node $node
     */
    public function isName($node, string $desiredName): bool
    {
        if (is_string($node)) {
            return $node === $desiredName;
        }

        if ($node instanceof Name || $node instanceof Identifier) {
            return (string) $node === $desiredName;
        }

        return false;
    }
}
