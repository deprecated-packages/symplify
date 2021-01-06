<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class FirstClassWithParameter
{
    public function method($x)
    {
        echo 'statement';
        return $x->execute() && $x->getResult();
    }
}
