<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ValueObjectOverArrayShapeRule\Fixture;

use Exception;

final class SkipJsonSerializable implements \Serializable
{
    /**
     * @return array{line: int}
     */
    public function run()
    {
    }

    public function serialize()
    {
    }

    public function unserialize($data)
    {
    }
}
