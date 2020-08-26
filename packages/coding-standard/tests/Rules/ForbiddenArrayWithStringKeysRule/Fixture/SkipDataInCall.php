<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipDataInCall
{
    public function someConfiguration()
    {
        $this->someMethod([
            'key' => 'value'
        ]);
    }

    public function someMethod(array $options)
    {

    }
}
