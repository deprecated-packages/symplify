<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;

final class AttrFinder
{
    /**
     * @param ClassMethod|Property|Class_ $node
     * @return Attribute[]
     */
    public function extra(Node $node): array
    {
        $attrs = [];
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $attrs[] = $attr;
            }
        }

        return $attrs;
    }
}
