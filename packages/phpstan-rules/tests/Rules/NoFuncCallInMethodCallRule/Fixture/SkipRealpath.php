<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\Fixture;

final class SkipRealpath
{
    public function run()
    {
        $this->container = $containerFactory->create(realpath(sys_get_temp_dir()), $existingAdditionalConfigFiles, []);
    }
}
