<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\RobotLoader;

use Nette\Loaders\RobotLoader;

final class PhpClassLoader
{
    /**
     * @param string[] $directories
     * @return array<string, string>
     */
    public function load(array $directories): array
    {
        $robotLoader = new RobotLoader();
        $robotLoader->addDirectory(...$directories);
        $robotLoader->setTempDirectory(sys_get_temp_dir() . '/migrify_psr4_switcher');
        $robotLoader->rebuild();

        return $robotLoader->getIndexedClasses();
    }
}
