<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipDataInConstantDefinition
{
    public const DEFAULT_DATA = [
        'key' => 'value'
    ];
}
