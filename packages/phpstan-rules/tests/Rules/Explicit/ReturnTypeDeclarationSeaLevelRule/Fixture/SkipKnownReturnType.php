<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ReturnTypeDeclarationSeaLevelRule\Fixture;

final class SkipKnownReturnType
{
    public function run(): int
    {
        return 1000;
    }
}
