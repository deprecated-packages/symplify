<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\Source\ReturnArray;

final class SkipArrayRequiredParentContract implements ReturnArray
{
    public function getData(): array
    {
        return [
            'key' => 'value',
        ];
    }
}
