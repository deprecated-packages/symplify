<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SecondClassDuplicateFirstClassWithParameterMethod
{
    public function method($y)
    {
        echo 'statement';
        return $y->execute() && $y->getResult();
    }
}
