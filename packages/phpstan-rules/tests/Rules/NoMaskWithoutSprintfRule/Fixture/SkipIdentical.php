<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMaskWithoutSprintfRule\Fixture;

final class SkipIdentical
{
    public function run(string $var)
    {
        return $var === '%s%s';
    }
}
