<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\Fixture;

interface InterfaceWithReturnType
{
    public function vote(): bool;
}
