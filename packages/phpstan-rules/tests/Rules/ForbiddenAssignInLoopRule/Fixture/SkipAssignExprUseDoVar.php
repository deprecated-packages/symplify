<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignExprUseDoVar
{
    public function run()
    {
        do {
            $value = new SmartFileInfo($i);
        } while ($i++ < 10);
    }
}
