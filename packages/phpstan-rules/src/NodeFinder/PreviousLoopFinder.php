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
    public function isUsedInPreviousLoop(array $variables, Node $node): bool
    {
        $previous = $node->getAttribute(PHPStanAttributeKey::PREVIOUS);
        if (! $previous instanceof Node) {
            $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
            if (! $parent instanceof Node) {
                return false;
            }

            return $this->isUsedInPreviousLoop($variables, $parent);
        }

        foreach ($variables as $variable) {
            $isInPrevious = (bool) $this->nodeFinder->findFirst($previous, function (Node $n) use ($variable): bool {
                return $this->simpleNameResolver->areNamesEqual($n, $variable);
            });
            if ($isInPrevious) {
                return true;
            }
        }

        return false;
    }
}
