<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipNoAssign
{
    public function run()
    {
        foreach ($data as $d) {

        }
    }
}
