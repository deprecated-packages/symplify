<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule\Fixture;

final class SkipNonStaticProperty
{
    protected $customFileNames = [];

    public function getCustomFileNames(): array
    {
        return $this->customFileNames;
    }
}
