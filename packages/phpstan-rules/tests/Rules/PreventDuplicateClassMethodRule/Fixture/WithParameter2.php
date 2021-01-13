<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

final class WithParameter2
{
    public function diff($param)
    {
        echo 'statement';
        $param->run('.php');
    }
}