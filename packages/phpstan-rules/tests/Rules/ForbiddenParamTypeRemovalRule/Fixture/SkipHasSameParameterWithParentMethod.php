<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Fixture;

class SkipHasSameParameterWithParentMethod extends ParentClass
{
    public function execute(string $string, int $int)
    {
    }
}
