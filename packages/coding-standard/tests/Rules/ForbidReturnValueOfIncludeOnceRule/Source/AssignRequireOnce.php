<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbidReturnValueOfIncludeOnceRule\Source;

final class AssignRequireOnce
{
    public function run(): void
    {
        $result = require_once 'Test.php';
    }
}
