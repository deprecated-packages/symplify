<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoArrayAccessOnObjectRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoArrayAccessOnObjectRule\Source\ChildOfSomeClassWithArrayAccess;

final class ArrayAccessOnNestedObject
{
    public function run()
    {
        $someClassWithArrayAcces = new ChildOfSomeClassWithArrayAccess();
        return $someClassWithArrayAcces['key'];
    }
}
