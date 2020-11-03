<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\BoolishClassMethodPrefixRule\Fixture;

class ClassThatImplementsInterface implements InterfaceWithReturnType
{
    public function vote(): bool
    {
        return (bool) 'a';
    }
}
