<?php

declare(strict_types=1);

namespace Symplify\NetteAdapterForSymfonyBundles\Tests;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\Utils\FileSystem;

final class ContainerFactory
{
    public function create() : Container
    {
        return $this->createWithConfig(__DIR__ . '/config/default.neon');
    }

    public function createWithConfig(string $config) : Container
    {
        $configurator = new Configurator();
        $configurator->addConfig($config);
        $configurator->setTempDirectory($this->createAndReturnTempDir());

        return $configurator->createContainer();
    }

    public static function createAndReturnTempDir() : string
    {
        $tempDir = sys_get_temp_dir() . '/nette-adapter-for-symfony-bundles';
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);

        return $tempDir;
    }
}
