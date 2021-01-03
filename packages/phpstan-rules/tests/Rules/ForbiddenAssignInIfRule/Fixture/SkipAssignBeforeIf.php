<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInIfRule\Fixture;

final class SkipAssignBeforeIf
{
    public function run()
    {
        function data()
        {
            return rand(1, 2);
        }

        $a = data();
        if ($a === 1) {

        }
    }
}
