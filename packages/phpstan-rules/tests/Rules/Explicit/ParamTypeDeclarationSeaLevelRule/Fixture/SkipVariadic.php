<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ParamTypeDeclarationSeaLevelRule\Fixture;

final class SkipVariadic
{
    public function run(... $items)
    {
    }
}
