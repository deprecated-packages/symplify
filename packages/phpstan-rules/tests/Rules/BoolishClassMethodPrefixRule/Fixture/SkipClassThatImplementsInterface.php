<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\Fixture;

class SkipClassThatImplementsInterface implements InterfaceWithReturnType
{
    public function vote(): bool
    {
        return (bool) 'a';
    }
}
