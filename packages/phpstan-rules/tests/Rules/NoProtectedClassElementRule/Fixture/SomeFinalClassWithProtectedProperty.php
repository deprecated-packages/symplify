<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedClassElementRule\Fixture;

final class SomeFinalClassWithProtectedProperty
{
    protected $x = [];
}
