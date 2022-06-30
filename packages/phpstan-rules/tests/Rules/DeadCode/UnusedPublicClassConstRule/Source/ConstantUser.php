<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Source;

use Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Fixture\SkipUsedPublicConstant;

final class ConstantUser
{
    public function run()
    {
        return SkipUsedPublicConstant::USED;
    }
}
