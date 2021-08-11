<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ValueObjectOverArrayShapeRule\Fixture;

final class SomeArrayShapeReturn
{
    /**
     * @return array{line: int}
     */
    public function run()
    {
    }
}
