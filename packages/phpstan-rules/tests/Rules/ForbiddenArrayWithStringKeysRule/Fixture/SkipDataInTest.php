<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipDataInTest
{
    public function run()
    {
        return [
            'key' => 'value'
        ];
    }
}
