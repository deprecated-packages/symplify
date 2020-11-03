<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireThisOnParentMethodCallRule\Fixture;

class CallParentMethodStaticallyWhenMethodOverriden extends ParentClass
{
    public function run()
    {
        parent::foo();
        parent::bar();
    }

    public function foo()
    {
    }

    public function bar()
    {
    }
}
