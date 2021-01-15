<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredMethodCallOverIdenticalCompareRule\Fixture;

use PhpParser\Node;

abstract class AbstractRector
{
    public function getName(Node $node)
    {
    }

    public function isName(Node $node, string $value)
    {
    }
}
