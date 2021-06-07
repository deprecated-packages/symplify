<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\Fixture;

final class SkipVoidSetter
{
    private $name;

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
