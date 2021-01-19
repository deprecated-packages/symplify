<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class AssignInLoop
{
    public function run()
    {
        foreach ($queries as $query) {
            $value = new SmartFileInfo('a.php');
            if ($value) {
            }
        }

        for ($i = 1; $i <=10; $i++) {
            $value = new SmartFileInfo('a.php');
            if ($value) {
            }
        }
    }
}
