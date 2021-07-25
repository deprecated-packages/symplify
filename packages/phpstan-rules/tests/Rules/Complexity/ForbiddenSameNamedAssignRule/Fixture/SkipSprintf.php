<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipSprintf
{
    public function run()
    {
        $message = sprintf('%s!', 'hey') ;
        $message = sprintf('%s!', 'hou');
    }
}
