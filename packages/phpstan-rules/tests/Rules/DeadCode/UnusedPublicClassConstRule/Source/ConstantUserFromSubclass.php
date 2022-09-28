<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Source;

use Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Fixture\SkipUsedPublicConstant;
use Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Fixture\SkipUsedPublicConstantInSubclass;

final class ConstantUserFromSubclass
{
    public function run2()
    {
        return SkipUsedPublicConstantInSubclass::USED_FROM_SUBCLASS;
    }
}
