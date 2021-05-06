<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoConstantInterfaceRule\Fixture;

interface InterfaceWithConstants
{
    public const SOME_KEY = 'value';
}
