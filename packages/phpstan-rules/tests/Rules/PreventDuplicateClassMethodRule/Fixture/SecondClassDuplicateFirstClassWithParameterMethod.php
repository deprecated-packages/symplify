<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SecondClassDuplicateFirstClassWithParameterMethod
{
    public function someMethod($b)
    {
        return $b->execute();
    }
}
