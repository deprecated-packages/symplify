<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Fixture;

final class SkipPrivateConstant
{
    private const DO_NO_TELL = 'not here';
}
