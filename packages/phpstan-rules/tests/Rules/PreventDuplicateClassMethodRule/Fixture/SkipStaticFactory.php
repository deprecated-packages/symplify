<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SkipStaticFactory
{
    public static function factory(): self {
        return new static();
    }
    public static function otherFactory(): self {
        return new static();
    }
}
