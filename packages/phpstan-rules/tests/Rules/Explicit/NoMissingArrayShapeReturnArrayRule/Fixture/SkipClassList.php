<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingArrayShapeReturnArrayRule\Fixture;

use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;

final class SkipClassList
{
    public function run()
    {
        return [String_::class, LNumber::class];
    }
}
