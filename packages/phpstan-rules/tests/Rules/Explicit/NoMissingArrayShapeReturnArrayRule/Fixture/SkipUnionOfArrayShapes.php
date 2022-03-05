<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingArrayShapeReturnArrayRule\Fixture;

final class SkipUnionOfArrayShapes
{
    /**
     * @return array{name: string}|array{surname: string}
     */
    public function run(string $name)
    {
        if (strlen($name) > 10) {
            return ['name' => $name];
        }

        return ['surname' => $name];
    }
}
