<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\Fixture;

use PhpParser\Node\Stmt\Nop;

final class SkipNode
{
    public function run()
    {
        $someValueObject = new Nop();
        return $someValueObject;
    }
}
