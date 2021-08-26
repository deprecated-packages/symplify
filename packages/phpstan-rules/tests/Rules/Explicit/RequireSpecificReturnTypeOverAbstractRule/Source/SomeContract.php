<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule\Source;

use PHPStan\Rules\Rule;

interface SomeContract
{
    public function getRule(): Rule;
}
