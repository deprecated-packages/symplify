<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoReferenceRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoReferenceRule\Source\AbstractSomeParentClassWithReference;

final class SkipParentMethodWithReference extends AbstractSomeParentClassWithReference
{
    public function someMethod(&$useIt)
    {
    }
}
