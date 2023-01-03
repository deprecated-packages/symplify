<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedClassElementRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoProtectedClassElementRule\Source\ClassUsingTraitWithProtectedAbstractMethod;

final class SkipKernelProtectedMethod extends ClassUsingTraitWithProtectedAbstractMethod
{
    protected function someMethod()
    {
    }
}
