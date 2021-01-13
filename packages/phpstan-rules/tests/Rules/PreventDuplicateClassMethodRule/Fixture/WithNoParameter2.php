<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

final class WithNoParameter2
{
    public function diff()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}