<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisCallOnLocalMethodRule\Fixture;

class SkipCallParentMethodStatically extends ParentClass
{
    public function run()
    {
        parent::foo();
        parent::bar();
    }
}
