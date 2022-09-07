<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ValueObjectOverArrayShapeRule\Fixture;

final class SkipIteratorContract
{
    /**
     * @return \Iterator<array{string, int}>
     */
    public function getItems(): \Iterator
    {
    }
}
