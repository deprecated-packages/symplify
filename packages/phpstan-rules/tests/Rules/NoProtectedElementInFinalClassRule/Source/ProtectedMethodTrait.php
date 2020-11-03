<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedElementInFinalClassRule\Source;

trait ProtectedMethodTrait
{
    protected abstract function someMethod();
}
