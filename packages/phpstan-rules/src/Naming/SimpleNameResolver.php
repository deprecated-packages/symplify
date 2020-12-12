<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Naming;

use Nette\Utils\Strings;
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

    /**
     * @param string[] $desiredNames
     */
    public function isNames(Node $node, array $desiredNames): bool
    {
        foreach ($desiredNames as $desiredName) {
            if ($this->isName($node, $desiredName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string|Node $node
     */
    public function isName($node, string $desiredName): bool
    {
        $name = $this->getName($node);
        if ($name === null) {
            return false;
        }

        if (Strings::contains($desiredName, '*')) {
            return fnmatch($desiredName, $name);
        }

        return $name === $desiredName;
    }

    public function areNamesEqual(Node $firstNode, Node $secondNode): bool
    {
        $firstName = $this->getName($firstNode);
        if ($firstName === null) {
            return false;
        }

        $secondName = $this->getName($secondNode);
        return $firstName === $secondName;
    }
}
