<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class AssignInForeach
{
    public function run()
    {
        foreach ($queries as $query) {
            $value = new SmartFileInfo('a.php');
            if ($value) {
            }
        }
    }
}
