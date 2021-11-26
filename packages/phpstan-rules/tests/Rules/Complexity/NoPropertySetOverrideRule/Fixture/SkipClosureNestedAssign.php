<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule\Source\SomeClass;

final class SkipClosureNestedAssign
{
    public function run(SomeClass $someClass)
    {
        if (mt_rand(0, 100)) {
            $someClass->anotherProperty = 100;
        }

        $closure = function(SomeClass $someClass) {
            $someClass->anotherProperty = 1000;
        };
    }
}
