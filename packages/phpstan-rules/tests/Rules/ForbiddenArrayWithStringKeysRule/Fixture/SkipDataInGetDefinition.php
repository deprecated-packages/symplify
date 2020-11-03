<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipDataInGetDefinition
{
    public function getDefinition()
    {
        return [
            'key' => 'value'
        ];
    }
}
