<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SecondClassDuplicateFirstClassWithParameterMethod
{
    public function method($b)
    {
        echo 'statement';
        return $b->execute() && $b->getResult();
    }
}
