<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMissingDirPathRule\Fixture;

final class SkipFnMatch
{
    public function run()
    {
        $missingFile = __DIR__ . '/FileMissing/*';
    }
}
