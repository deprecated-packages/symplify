<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Source\ReturnArray;

final class SkipArrayRequiredParentContract implements ReturnArray
{
    public function getData(): array
    {
        return [
            'key' => 'value',
        ];
    }
}
