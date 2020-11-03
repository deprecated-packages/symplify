<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoMissingDirPathRule\Fixture;

final class SkipFileExistsFuncCallOneLayerAbove
{
    public function run()
    {
        if (file_exists(__DIR__ . '/not_here.php')) {
            require_once __DIR__ . '/not_here.php';
        }
    }
}
