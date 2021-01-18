<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignExprUseLoopVar
{
    public function run()
    {
        foreach ($data as $d) {
            $value = new SmartFileInfo($d);
        }
    }
}
