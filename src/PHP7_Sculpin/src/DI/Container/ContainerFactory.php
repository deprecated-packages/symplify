<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\DI\Container;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\Utils\FileSystem;

final class ContainerFactory
{
    public function create() : Container
    {
        return $this->createWithConfig(__DIR__ . '/../../config/config.neon');
    }

    public function createWithConfig(string $configFilePath) : Container
    {
        $configurator = new Configurator();
        $configurator->setDebugMode(true);
        $configurator->setTempDirectory($this->createAndReturnTempDir());
        $configurator->addConfig($configFilePath);

        return $configurator->createContainer();
    }

    private function createAndReturnTempDir() : string
    {
        $tempDir = sys_get_temp_dir() . '/php7_sculpin';
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);

        return $tempDir;
    }
}
