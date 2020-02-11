<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\ClassMethod\Source;

class SkipRequiredByInterface implements InterfaceWithReturnType
{
    public function vote(): bool
    {
        return true;
    }
}
