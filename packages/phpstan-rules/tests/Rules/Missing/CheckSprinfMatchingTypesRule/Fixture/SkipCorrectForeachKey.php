<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprinfMatchingTypesRule\Fixture;

final class SkipCorrectForeachKey
{
    public function run()
    {
        $values = [
            'key' => 'value',
            100 => '1'
        ];

        $messages = [];
        foreach ($values as $key => $value) {
            $messages[] = sprintf('String key %s', $key);
        }
    }
}
