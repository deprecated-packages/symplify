<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class DifferentMethodName1
{
    public function go($value)
    {
        $value += 100000;
        return $value + 1000;
    }
}