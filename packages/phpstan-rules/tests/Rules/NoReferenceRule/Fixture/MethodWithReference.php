<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReferenceRule\Fixture;

final class MethodWithReference
{
    public function someMethod(&$useIt)
    {
    }
}
