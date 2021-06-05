<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SkipSelfFactory
{
    public static function factory(): self {
        return new self();
    }
    public static function otherFactory(): self {
        return new self();
    }
}
