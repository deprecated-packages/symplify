<?php

declare(strict_types=1);

namespace Symplify\Astral\Naming;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Property;
use Symplify\Astral\Contract\NodeNameResolverInterface;

/**
 * @see \Symplify\Astral\Tests\Naming\SimpleNameResolverTest
 */
final class SimpleNameResolver
{
    /**
     * @see https://regex101.com/r/ChpDsj/1
     * @var string
     */
    public const ANONYMOUS_CLASS_REGEX = '#^AnonymousClass[\w+]#';

    /**
     * @param NodeNameResolverInterface[] $nodeNameResolvers
     */
    public function __construct(
        private array $nodeNameResolvers
    ) {
    }

    public function getName(Node | string $node): ?string
    {
        if (is_string($node)) {
            return $node;
        }

        foreach ($this->nodeNameResolvers as $nodeNameResolver) {
            if (! $nodeNameResolver->match($node)) {
                continue;
            }

            return $nodeNameResolver->resolve($node);
        }

        if ($node instanceof ClassConstFetch && $this->isName($node->name, 'class')) {
            return $this->getName($node->class);
        }

        if ($node instanceof Property) {
            $propertyProperty = $node->props[0];
            return $this->getName($propertyProperty->name);
        }

        if ($node instanceof Variable) {
            return $this->getName($node->name);
        }

        return null;
    }

    public function isName(string | Node $node, string $desiredName): bool
    {
        $name = $this->getName($node);
        if ($name === null) {
            return false;
        }

        if (\str_contains($desiredName, '*')) {
            return fnmatch($desiredName, $name);
        }

        return $name === $desiredName;
    }

    /**
     * @api
     */
    public function areNamesEqual(Node $firstNode, Node $secondNode): bool
    {
        $firstName = $this->getName($firstNode);
        if ($firstName === null) {
            return false;
        }

        return $this->isName($secondNode, $firstName);
    }

    /**
     * @api
     */
    public function isNameMatch(Node $node, string $desiredNameRegex): bool
    {
        $name = $this->getName($node);
        if ($name === null) {
            return false;
        }

        return (bool) Strings::match($name, $desiredNameRegex);
    }
}
