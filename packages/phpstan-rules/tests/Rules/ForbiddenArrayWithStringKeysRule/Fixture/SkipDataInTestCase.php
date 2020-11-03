<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipDataInTestCase
{
    public function run()
    {
        return [
            'key' => 'value'
        ];
    }
}
