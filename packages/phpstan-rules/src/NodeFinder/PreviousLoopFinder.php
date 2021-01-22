<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;

final class PreviousLoopFinder
{
    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(NodeFinder $nodeFinder, SimpleNameResolver $simpleNameResolver)
    {
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @param Variable[] $variables
     */
    public function isUsedInPreviousLoop(array $variables, Node $desiredNode): bool
    {
        $previous = $desiredNode->getAttribute(PHPStanAttributeKey::PREVIOUS);
        if (! $previous instanceof Node) {
            $parent = $desiredNode->getAttribute(PHPStanAttributeKey::PARENT);
            if (! $parent instanceof Node) {
                return false;
            }

            return $this->isUsedInPreviousLoop($variables, $parent);
        }

        foreach ($variables as $variable) {
            $isInPrevious = (bool) $this->nodeFinder->findFirst($previous, function (Node $node) use ($variable): bool {
                return $this->simpleNameResolver->areNamesEqual($node, $variable);
            });

            if ($isInPrevious) {
                return true;
            }
        }

        return false;
    }
}
