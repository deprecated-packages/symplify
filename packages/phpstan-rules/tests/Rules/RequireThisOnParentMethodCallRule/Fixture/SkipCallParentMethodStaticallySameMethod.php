<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule\Fixture;

class SkipCallParentMethodStaticallySameMethod extends ParentClass
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
