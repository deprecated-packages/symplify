<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class DifferentMethodName2
{
    public function sleep($yet)
    {
        $yet += 100000;
        return $yet + 1000;
    }
}