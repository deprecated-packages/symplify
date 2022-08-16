<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ParamTypeDeclarationSeaLevelRule\Fixture;

final class SkipKnownParamType
{
    public function run(string $name, int $age)
    {
    }
}
