<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\Source;

trait ProtectedMethodTrait
{
    protected abstract function someMethod();
}
