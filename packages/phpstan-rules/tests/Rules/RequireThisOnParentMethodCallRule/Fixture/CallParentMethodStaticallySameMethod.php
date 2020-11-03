<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireThisOnParentMethodCallRule\Fixture;

class CallParentMethodStaticallySameMethod extends ParentClass
{
    public function foo()
    {
        parent::foo();

        echo 'override';
    }

    public function bar()
    {
        parent::bar();

        echo 'override';
    }
}
