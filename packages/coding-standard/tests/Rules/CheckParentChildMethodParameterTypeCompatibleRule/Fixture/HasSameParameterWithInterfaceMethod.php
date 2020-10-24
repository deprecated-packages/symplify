<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

class HasSameParameterWithInterfaceMethod implements AnInterface
{
    public function execute(string $string, int $int)
    {
    }
}
