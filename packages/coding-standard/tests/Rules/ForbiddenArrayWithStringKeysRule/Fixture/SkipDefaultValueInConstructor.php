<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipDefaultValueInConstructor
{
    private $values = [];

    public function __construct()
    {
        $this->values = [
            'key' => 'value'
        ];
    }
}
