<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoGetterAndPropertyRule\Fixture;

final class SkipPrivateMethod
{
    public $name;

    private function getName()
    {
        return $this->name;
    }
}
