<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SecondClassDuplicateFirstClassWithParameterMethod
{
    /**
     * @param object $y
     */
    public function method(object $y)
    {
        echo 'statement';
        return $y->execute() && $y->getResult();
    }
}
