<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipDataInGetDefinition
{
    public function getDefinition()
    {
        return [
            'key' => 'value'
        ];
    }
}
