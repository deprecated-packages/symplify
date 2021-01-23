<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignExprUseForeachVar
{
    public function run()
    {


        foreach ($data as $key => $d) {
            $value = new SmartFileInfo($key);
        }
    }
}
