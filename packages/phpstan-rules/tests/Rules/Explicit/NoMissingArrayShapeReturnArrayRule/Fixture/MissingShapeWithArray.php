<?php

declare(strict_types = 1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingArrayShapeReturnArrayRule\Fixture;

final class MissingShapeWithArray
{
    public function run(string $name): array
    {
        return ['name' => $name];
    }
}
