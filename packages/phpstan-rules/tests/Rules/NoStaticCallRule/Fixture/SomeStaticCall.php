<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule\Source\SomeMethods;

final class SomeStaticCall
{
    public function someMethod()
    {
        SomeMethods::stand();
    }
}
