<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedElementInFinalClassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoProtectedElementInFinalClassRule\Source\ClassUsingTraitWithProtectedAbstractMethod;

final class SkipKernelProtectedMethod extends ClassUsingTraitWithProtectedAbstractMethod
{
    protected function someMethod()
    {
    }
}
