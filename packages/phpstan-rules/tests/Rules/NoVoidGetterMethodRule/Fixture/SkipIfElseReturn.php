<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoVoidGetterMethodRule\Fixture;

final class SkipIfElseReturn
{
    public function get()
    {
        if (mt_rand(0, 1)) {
            return true;
        } else {
            return false;
        }
    }
}
