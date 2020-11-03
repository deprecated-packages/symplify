<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoTraitExceptItsMethodsPublicAndRequiredRule\Fixture;

trait SomeTraitWithPublicMethod
{
    public function run()
    {
    }
}
