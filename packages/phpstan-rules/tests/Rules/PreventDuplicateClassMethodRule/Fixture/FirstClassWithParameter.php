<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class FirstClassWithParameter
{
    public function someMethod($a)
    {
        return $a->execute();
    }
}
