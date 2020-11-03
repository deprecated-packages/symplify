<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoConstructorInTestRule\Fixture;

final class SomeClass
{
    public function __construct()
    {
    }
}
