<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Stmt\If_;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\ValueObject\AttributeKey;

final class IfEnumAnalyzer
{
    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder
    ) {
    }

    public function isMultipleIf(Node $node): bool
    {
        if (! $node instanceof If_) {
            return false;
        }

        $parent = $node->getAttribute(AttributeKey::PARENT);

        $ifs = $this->simpleNodeFinder->findByType($parent, If_::class);

        // might be dangerous
        return count($ifs) > 1;
    }
}
