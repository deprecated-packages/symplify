<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\ClassMethod\Source;

class ClassThatImplementsInterface implements InterfaceWithReturnType
{
    public function nothing(): bool
    {
        return (bool) 'a';
    }
}
