<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipAssignNotInLoop
{
    public function run()
    {
        $value = new SmartFileInfo('a.php');
    }
}
