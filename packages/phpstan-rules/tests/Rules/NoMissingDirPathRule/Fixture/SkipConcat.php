<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMissingDirPathRule\Fixture;

final class SkipConcat
{
    public function run($environment)
    {
        $missingFile = __DIR__ . '/directory/' . $environment;
    }
}
