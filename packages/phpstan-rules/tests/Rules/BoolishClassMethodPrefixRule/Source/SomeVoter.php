<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\Source;

abstract class SomeVoter
{
    public function vote(): bool
    {
        return false;
    }
}
