<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipJsonNamed
{
    public function provide()
    {
        return [
            'name' => 'yes',
        ];
    }
}
