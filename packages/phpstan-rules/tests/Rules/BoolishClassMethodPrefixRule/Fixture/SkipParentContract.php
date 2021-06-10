<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\Source\SomeVoter;

final class SkipParentContract extends SomeVoter
{
    public function vote(): bool
    {
        return false;
    }
}
