<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;

final class IfEnumAnalyzer
{
    public function __construct(
        private NodeFinder $nodeFinder
    ) {
    }

    public function isMultipleIf(Node $node, Node $parentNode): bool
    {
        if (! $node instanceof If_) {
            return false;
        }

        /** @var If_[] $ifs */
        $ifs = $this->nodeFinder->findInstanceOf($parentNode, If_::class);

        // might be dangerous
        return count($ifs) > 1;
    }
}
