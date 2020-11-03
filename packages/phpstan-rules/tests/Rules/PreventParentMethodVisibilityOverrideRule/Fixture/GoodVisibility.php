<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventParentMethodVisibilityOverrideRule\Fixture;

abstract class GoodVisibility
{
    protected function run()
    {

    }
}
