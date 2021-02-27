<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoModifyAndReturnSelfObjectRule\Fixture;

use PhpParser\Node;

final class SkipStringUnion
{
    /**
     * @param Node|string $node
     */
    public function getName($node): ?string
    {
        if (is_string($node)) {
            return $node;
        }

        return null;
    }
}
