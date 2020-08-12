<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreventParentMethodVisibilityOverrideRule\Source;

final class ClassWithOverridingVisibility extends GoodVisibility
{
    public function run()
    {
    }
}
