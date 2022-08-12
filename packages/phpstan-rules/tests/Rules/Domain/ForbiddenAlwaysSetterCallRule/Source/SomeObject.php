<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Source;

final class SomeObject
{
    public function setName(string $name)
    {
    }
}
