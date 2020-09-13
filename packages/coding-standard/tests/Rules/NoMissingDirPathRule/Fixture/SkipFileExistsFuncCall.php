<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoMissingDirPathRule\Fixture;

final class SkipFileExistsFuncCall
{
    public function run()
    {
        if (file_exists(__DIR__ . '/not_here.php')) {
            return true;
        }
    }
}
