<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Finder;

use Nette\Loaders\RobotLoader;
use ReflectionClass;

final class ClassByTypeFinder
{
    /**
     * @param string[] $directories
     * @return string[]
     */
    public function findByType(array $directories, string $type): array
    {
        $robotLoader = new RobotLoader();
        $robotLoader->setTempDirectory(sys_get_temp_dir() . '/robot_loader_temp');
        $robotLoader->addDirectory(...$directories);
        $robotLoader->ignoreDirs[] = '*tests*';
        $robotLoader->ignoreDirs[] = '*Fixture*';
        $robotLoader->ignoreDirs[] = '*templates*';

        $robotLoader->rebuild();

        $desiredClasses = [];
        foreach (array_keys($robotLoader->getIndexedClasses()) as $class) {
            if (! is_a($class, $type, true)) {
                continue;
            }

            // skip abstract classes
            $reflectionClass = new ReflectionClass($class);
            if ($reflectionClass->isAbstract()) {
                continue;
            }

            $desiredClasses[] = $class;
        }

        sort($desiredClasses);

        return $desiredClasses;
    }
}
