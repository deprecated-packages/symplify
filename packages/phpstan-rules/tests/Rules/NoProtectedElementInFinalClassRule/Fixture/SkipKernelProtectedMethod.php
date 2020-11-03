<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\Source\ClassUsingTraitWithProtectedAbstractMethod;

final class SkipKernelProtectedMethod extends ClassUsingTraitWithProtectedAbstractMethod
{
    protected function someMethod()
    {
    }
}
