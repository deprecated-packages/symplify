<?php declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\ServiceClass;

use Nette\Caching\Storages\DevNullStorage;
use Nette\Loaders\RobotLoader;

final class ServiceClassFinder
{
    /**
     * @return string[]
     */
    public function findServicesInDirsByClassSuffix(array $dirs, array $classSuffixesToSeek) : array
    {
        $robot = new RobotLoader();
        $robot->setCacheStorage(new DevNullStorage());
        foreach ($dirs as $dir) {
            $robot->addDirectory($dir);
        }
        $robot->ignoreDirs .= ', Tests';
        $robot->acceptFiles = '*' . implode('.php,*', $classSuffixesToSeek) . '.php';
        $robot->rebuild();

        return array_keys($robot->getIndexedClasses());
    }
}
