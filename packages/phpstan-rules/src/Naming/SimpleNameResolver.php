<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\PHPStan\Naming;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Property;

final class SimpleNameResolver
{
    /**
     * @param Node|string $node
     */
    public function getName($node): ?string
    {
        if ($node instanceof Property) {
            $propertyProperty = $node->props[0];
            return $this->getName($propertyProperty->name);
        }

        if ($node instanceof Variable) {
            return $this->getName($node->name);
        }

        if ($node instanceof Expr) {
            return null;
        }

        if ($node instanceof Identifier) {
            return (string) $node;
        }

        if ($node instanceof Name) {
            return (string) $node;
        }

        if (is_string($node)) {
            return $node;
        }

        return null;
    }
}
