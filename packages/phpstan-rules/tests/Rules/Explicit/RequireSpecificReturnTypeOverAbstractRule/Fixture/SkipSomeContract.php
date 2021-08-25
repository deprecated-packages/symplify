<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule\Fixture;

use PHPStan\Rules\Cast\EchoRule;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule\Source\SomeContract;

final class SkipSomeContract implements SomeContract
{
    public function getRule(): Rule
    {
        return new EchoRule();
    }
}
