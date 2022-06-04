<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer\Fixture;

final class SkipPropertyPromotion
{
    public function __construct(
        public int $value,
        public int $name
    ) {
    }
}
