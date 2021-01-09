<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule\Fixture;

class SkipCallParentMethodStaticallyWhenMethodOverriden extends ParentClass
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
