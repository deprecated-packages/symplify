<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

use PhpParser\Node;

final class SkipParentNotIf
{
    public function run(Node $node)
    {
        $this->execute($node);
    }
}
