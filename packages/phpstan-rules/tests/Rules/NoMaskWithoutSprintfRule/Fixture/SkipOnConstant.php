<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMaskWithoutSprintfRule\Fixture;

final class SkipOnConstant
{
    public const SOME_CONST ='Hey %s';
}
