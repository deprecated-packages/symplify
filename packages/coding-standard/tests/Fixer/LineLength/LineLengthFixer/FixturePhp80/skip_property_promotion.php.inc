<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer\FixturePhp80;

final class SkipPropertyPromotion
{
    public function __construct(
        public int $value,
        public int $name
    ) {
    }
}
