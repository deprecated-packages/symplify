<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Source;

use PhpParser\Node;

final class AnotherClassWithRun
{
    public function run(Node $node)
    {
        return $node;
    }
}
