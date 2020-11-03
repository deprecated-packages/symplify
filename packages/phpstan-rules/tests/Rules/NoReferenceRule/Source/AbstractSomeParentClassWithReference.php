<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReferenceRule\Source;

abstract class AbstractSomeParentClassWithReference
{
    public function someMethod(&$useIt)
    {
    }
}
