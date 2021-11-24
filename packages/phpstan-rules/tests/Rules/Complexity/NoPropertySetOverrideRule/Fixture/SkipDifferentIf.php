<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\NoPropertySetOverrideRule\Source\SomeClass;

final class SkipDifferentIf
{
    public function run()
    {
        $someClass = new SomeClass();
        if (\mt_rand(0, 1)) {
            $someClass->someProperty = 'one';
        }

        if (\mt_rand(0, 100)) {
            $someClass->someProperty = 'two';
        }

        return $someClass;
    }
}
