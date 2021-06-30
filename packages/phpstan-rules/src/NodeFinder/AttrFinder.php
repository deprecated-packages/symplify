<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node\Attribute;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;

final class AttrFinder
{
    /**
     * @return Attribute[]
     */
    public function extra(ClassMethod | Property | Class_ $node): array
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
