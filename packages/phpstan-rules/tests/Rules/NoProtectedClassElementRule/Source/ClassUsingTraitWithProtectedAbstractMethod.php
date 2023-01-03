<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedClassElementRule\Source;

abstract class ClassUsingTraitWithProtectedAbstractMethod
{
    use ProtectedMethodTrait;
}
