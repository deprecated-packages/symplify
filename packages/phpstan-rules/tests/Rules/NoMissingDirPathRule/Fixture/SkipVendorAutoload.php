<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoMissingDirPathRule\Fixture;

final class SkipVendorAutoload
{
    public function run()
    {
        $missingFile = __DIR__ . '/../vendor/autoload.php';
    }
}
