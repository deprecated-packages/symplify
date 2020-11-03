<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMissingDirPathRule\Fixture;

final class FileMissing
{
    public function run()
    {
        $missingFile = __DIR__ . '/not_here.php';
    }
}
