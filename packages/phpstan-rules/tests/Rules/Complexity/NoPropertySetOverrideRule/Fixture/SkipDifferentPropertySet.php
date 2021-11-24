<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule\Source\SomeClass;

final class SkipDifferentPropertySet
{
    public function run()
    {
        $someClass = new SomeClass();
        $someClass->someProperty = 'one';

        $someClass->anotherProperty = 'two';
    }
}
