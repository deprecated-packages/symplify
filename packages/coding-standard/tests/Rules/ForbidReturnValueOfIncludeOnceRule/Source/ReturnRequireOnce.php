<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbidReturnValueOfIncludeOnceRule\Source;

final class ReturnRequireOnce
{
    public function run()
    {
        return require_once 'Test.php';
    }
}
