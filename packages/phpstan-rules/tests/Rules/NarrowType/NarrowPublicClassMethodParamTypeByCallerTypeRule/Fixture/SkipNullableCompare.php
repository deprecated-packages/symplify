<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture;

use PhpParser\Node;

final class SkipNullableCompare
{
    public function callNode(?Node $node)
    {
    }
}
