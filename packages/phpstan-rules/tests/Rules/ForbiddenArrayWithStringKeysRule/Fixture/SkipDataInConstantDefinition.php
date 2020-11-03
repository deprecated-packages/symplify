<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipDataInConstantDefinition
{
    public const DEFAULT_DATA = [
        'key' => 'value'
    ];
}
