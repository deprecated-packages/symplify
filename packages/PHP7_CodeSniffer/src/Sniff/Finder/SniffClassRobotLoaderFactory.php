<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

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
