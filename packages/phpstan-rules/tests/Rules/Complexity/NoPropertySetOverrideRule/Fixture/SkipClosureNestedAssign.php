<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule\Source\SomeClass;

final class SkipClosureNestedAssign
{
    public function run(SomeClass $someClass)
    {
        $closure = function(SomeClass $someClass) {
            $someClass->anotherProperty = 1000;
        };

        $closure = function(SomeClass $someClass) {
            $someClass->anotherProperty = 1000;
        };
    }
}
