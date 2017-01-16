<?php

declare(strict_types=1);

namespace Symplify\MultiCodingStandard\Tests;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\Utils\FileSystem;

final class ContainerFactory
{
    public function create() : Container
    {
        return $this->createWithConfig(__DIR__.'/../src/config/config.neon');
    }

    public function createWithConfig($config) : Container
    {
        $configurator = new Configurator();
        $configurator->setTempDirectory($this->createAndReturnTempDir());
        $configurator->addConfig($config);

        return $configurator->createContainer();
    }

    private function createAndReturnTempDir() : string
    {
        $tempDir = sys_get_temp_dir().'/multi_cs';
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);

        return $tempDir;
    }
}
