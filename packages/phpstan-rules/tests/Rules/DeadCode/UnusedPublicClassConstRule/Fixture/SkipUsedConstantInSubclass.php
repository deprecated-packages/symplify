<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Fixture;

class PublicConstant
{
    public const USED_FROM_SUBCLASS = 'yes, please';
}

final class SkipUsedPublicConstantInSubclass extends PublicConstant
{
}
