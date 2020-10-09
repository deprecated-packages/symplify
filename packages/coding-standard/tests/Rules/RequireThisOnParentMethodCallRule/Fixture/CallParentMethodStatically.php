<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireThisOnParentMethodCallRule\Fixture;

class CallParentMethodStatically extends ParentClass
{
    public function run()
    {
        parent::foo();
        parent::bar();
    }
}
