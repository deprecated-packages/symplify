<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Fixture;

class PublicConstant
{
    public const USED = 'yes, please';
}

final class SkipUsedPublicConstantInSubclass extends PublicConstant
{
}
