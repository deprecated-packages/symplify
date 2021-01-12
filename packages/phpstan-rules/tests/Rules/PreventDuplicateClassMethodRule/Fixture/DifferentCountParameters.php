<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

final class WithNoParameter1
{
    public function diff()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}

final class WithNoParameter2
{
    public function diff()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}

final class WithParameter1
{
    public function diff($param)
    {
        echo 'statement';
        $param->run('.php');
    }
}

final class WithParameter2
{
    public function diff($param)
    {
        echo 'statement';
        $param->run('.php');
    }
}
