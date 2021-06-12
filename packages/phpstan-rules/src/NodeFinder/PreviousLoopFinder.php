<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\ValueObject\AttributeKey;

final class PreviousLoopFinder
{
    public function __construct(
        private NodeFinder $nodeFinder,
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @param Variable[] $variables
     */
    public function isUsedInPreviousLoop(array $variables, Node $desiredNode): bool
    {
        $previous = $desiredNode->getAttribute(AttributeKey::PREVIOUS);
        if (! $previous instanceof Node) {
            $parent = $desiredNode->getAttribute(AttributeKey::PARENT);
            if (! $parent instanceof Node) {
                return false;
            }

            return $this->isUsedInPreviousLoop($variables, $parent);
        }

        foreach ($variables as $variable) {
            $isInPrevious = (bool) $this->nodeFinder->findFirst(
                $previous,
                fn (Node $node): bool => $this->simpleNameResolver->areNamesEqual($node, $variable)
            );

            if ($isInPrevious) {
                return true;
            }
        }

        return false;
    }
}
