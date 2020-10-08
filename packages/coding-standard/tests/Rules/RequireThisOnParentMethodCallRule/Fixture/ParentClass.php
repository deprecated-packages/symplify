<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireThisOnParentMethodCallRule\Fixture;

class ParentClass
{
    protected function foo()
    {
        echo 'foo';
    }

    protected function bar()
    {
        echo 'bar';
    }
}
