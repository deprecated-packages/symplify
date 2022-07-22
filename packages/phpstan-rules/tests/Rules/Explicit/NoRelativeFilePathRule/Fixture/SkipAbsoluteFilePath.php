<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoRelativeFilePathRule\Fixture;

final class SkipAbsoluteFilePath
{
    public function run()
    {
        return __DIR__ . '/some_absolute_path.txt';
    }
}
