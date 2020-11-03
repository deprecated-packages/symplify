<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipNonConstantString
{
    public function run()
    {
        $value = 'key';
        return [
            $value => 'value'
        ];
    }
}
