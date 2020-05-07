<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\BoolishClassMethodPrefixRule\Source;

class SkipRequiredByInterface implements InterfaceWithReturnType
{
    public function vote(): bool
    {
        return true;
    }
}
