<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventParentMethodVisibilityOverrideRule\Fixture;

final class ClassWithOverridingVisibility extends GoodVisibility
{
    public function run()
    {
    }
}
