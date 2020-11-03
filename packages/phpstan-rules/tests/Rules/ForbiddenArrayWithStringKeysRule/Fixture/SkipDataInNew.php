<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\Source\ObjectWithOptions;

final class SkipDataInNew
{
    public function someConfiguration()
    {
        $value = new ObjectWithOptions([
            'key' => 'value'
        ]);
    }
}
