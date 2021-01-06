<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class FirstClassWithParameter
{
    public function method($a)
    {
        echo 'statement';
        return $a->execute() && $a->getResult();
    }
}
