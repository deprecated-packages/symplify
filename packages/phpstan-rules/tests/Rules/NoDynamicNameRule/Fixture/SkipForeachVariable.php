<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

final class SkipForeachVariable
{
    public function run(array $values)
    {
        foreach ($values['callables'] as $value) {
            return $value('input');
        }
    }
}
