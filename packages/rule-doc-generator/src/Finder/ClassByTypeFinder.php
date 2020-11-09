<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Finder;

use Nette\Loaders\RobotLoader;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassByTypeFinder
{
    /**
     * @return string[]
     */
    public function findByType(SmartFileInfo $directoryFileInfo, string $type): array
    {
        $robotLoader = new RobotLoader();
        $robotLoader->setTempDirectory(sys_get_temp_dir() . '/robot_loader_temp');
        $robotLoader->addDirectory($directoryFileInfo->getPathname());
        $robotLoader->ignoreDirs[] = '*tests*';
        $robotLoader->ignoreDirs[] = '*Fixture*';

        $robotLoader->rebuild();

        $desiredClasses = [];
        foreach (array_keys($robotLoader->getIndexedClasses()) as $class) {
            if (! is_a($class, $type, true)) {
                continue;
            }

            $desiredClasses[] = $class;
        }

        sort($desiredClasses);

        return $desiredClasses;
    }
}
