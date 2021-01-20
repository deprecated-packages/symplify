<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignVarExprUsedPreviousLoop
{
    public function run()
    {
        $a = 'x';
        do {
            $value = new SmartFileInfo($a);
            if ($value) {
                $a = 'y';
            }
        } while ($i++ < 10);
    }
}
