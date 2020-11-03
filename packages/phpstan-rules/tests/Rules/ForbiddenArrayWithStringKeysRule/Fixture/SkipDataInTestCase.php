<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipDataInTestCase
{
    public function run()
    {
        return [
            'key' => 'value'
        ];
    }
}
