<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Sniff\Finder;

use Nette\Caching\Storages\DevNullStorage;
use Nette\Loaders\RobotLoader;

final class SniffClassRobotLoaderFactory
{
    public function createForDirectory(string $directory) : RobotLoader
    {
        $robot = new RobotLoader();
        $robot->setCacheStorage(new DevNullStorage());
        $robot->addDirectory($directory);
        $robot->ignoreDirs .= ', tests, Tests';
        $robot->acceptFiles = '*Sniff.php';
        $robot->rebuild();

        return $robot;
    }
}
