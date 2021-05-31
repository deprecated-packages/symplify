<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\Fixture;

final class SkipSysGetTempDir
{
    public function run()
    {
        $this->container = $containerFactory->create(realpath(sys_get_temp_dir()), $existingAdditionalConfigFiles, []);
    }
}
