<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ValueObjectOverArrayShapeRule\Fixture;

final class SkipNoArrayShape
{
    /**
     * @return array[]
     */
    public function run()
    {
    }
}
