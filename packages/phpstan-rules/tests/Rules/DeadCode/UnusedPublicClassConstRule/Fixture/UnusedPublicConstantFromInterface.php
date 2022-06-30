<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Fixture;

interface UnusedPublicConstantFromInterface
{
    public const UNUSED = 'not here';
}
