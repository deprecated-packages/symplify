<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class AssignInDo
{
    public function run()
    {
        do {
            $value = new SmartFileInfo('a.php');
            if ($value) {
            }
        } while ($i++ < 10);
    }
}
