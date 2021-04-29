<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\TooDeepNewClassNestingRule\FixturePhp8;

use InvalidArgumentException;
use Symplify\PHPStanRules\Tests\Rules\TooDeepNewClassNestingRule\Source\SomeClassWithArguments;

final class SkipExpressionThrow
{
    /**
     * @var string
     */
    private const KEY = 'key';

    public function run($values): void
    {
        new SomeClassWithArguments(
            $values[self::KEY] ?? throw new InvalidArgumentException(),
            $values[self::KEY] ?? throw new InvalidArgumentException(),
            $values[self::KEY] ?? throw new InvalidArgumentException()
        );
    }
}
