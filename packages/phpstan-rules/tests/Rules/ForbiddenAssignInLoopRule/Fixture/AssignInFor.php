<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class AssignInFor
{
    public function run()
    {
        for ($i = 1; $i<=10; $i++) {
            $value = new SmartFileInfo('a.php');
            if ($value) {
            }
        }
    }
}
