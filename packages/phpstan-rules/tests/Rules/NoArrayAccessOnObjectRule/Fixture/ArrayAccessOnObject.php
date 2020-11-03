<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayAccessOnObjectRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoArrayAccessOnObjectRule\Source\SomeClassWithArrayAccess;

final class ArrayAccessOnObject
{
    public function run()
    {
        $someClassWithArrayAcces = new SomeClassWithArrayAccess();
        return $someClassWithArrayAcces['key'];
    }
}
