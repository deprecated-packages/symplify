<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\Fixture;

final class SkipYamlRelated
{
    public function getData(): array
    {
        return [
            'key' => 'value',
        ];
    }
}
