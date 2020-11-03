<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenReturnValueOfIncludeOnceRule\Fixture;

final class ReturnRequireOnce
{
    public function run()
    {
        return require_once 'Test.php';
    }
}
