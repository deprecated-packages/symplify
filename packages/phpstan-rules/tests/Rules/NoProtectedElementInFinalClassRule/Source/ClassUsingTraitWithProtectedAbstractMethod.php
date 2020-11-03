<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\Source;

abstract class ClassUsingTraitWithProtectedAbstractMethod
{
    use ProtectedMethodTrait;
}
