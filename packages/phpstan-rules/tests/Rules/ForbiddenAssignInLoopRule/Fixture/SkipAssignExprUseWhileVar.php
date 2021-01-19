<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignExprUseWhileVar
{
    public function run()
    {
        while ($i++ < 10) {
            $value = new SmartFileInfo($i);
        }
    }
}
