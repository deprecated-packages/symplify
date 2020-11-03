<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule\Source\AllowedMethods;

final class SkipAllowedStaticCall
{
    public function someMethod()
    {
        AllowedMethods::allowed();
    }
}
