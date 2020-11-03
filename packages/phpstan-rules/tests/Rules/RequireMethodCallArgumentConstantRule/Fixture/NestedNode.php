<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireMethodCallArgumentConstantRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Arg;

final class NestedNode
{
    public function isBeingCheckedIfExists(Node $node): bool
    {
        $parent = $node->getAttribute('parent');
        if (! $parent instanceof Arg) {
            return false;
        }

        return $parent->getAttribute('parent');
    }
}
