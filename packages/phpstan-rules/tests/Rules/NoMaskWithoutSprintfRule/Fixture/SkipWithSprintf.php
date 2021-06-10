<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMaskWithoutSprintfRule\Fixture;

final class SkipWithSprintf
{
    public function run()
    {
        return sprintf('Hey %s', 'Matthias');
    }
}
