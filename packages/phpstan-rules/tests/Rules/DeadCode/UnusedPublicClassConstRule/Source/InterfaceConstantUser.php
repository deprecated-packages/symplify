<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Source;

use Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassConstRule\Fixture\SkipInterfaceConstantUsed;

final class InterfaceConstantUser
{
    public function value()
    {
        return SkipInterfaceConstantUsed::STATUS_ERROR;
    }
}
