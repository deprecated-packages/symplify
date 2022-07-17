<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoRelativeFilePathRule\Fixture;

final class SkipStrEndsWith
{
    public function run($filePath)
    {
        return \str_ends_with($filePath, 'bundles.php');
    }
}
