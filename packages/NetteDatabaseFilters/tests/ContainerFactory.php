<?php declare(strict_types=1);

namespace Zenify\NetteDatabaseFilters\Tests;

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
        $configurator = new Configurator;
        $configurator->setTempDirectory($this->createAndReturnTempDir());
        $configurator->addConfig($config);
        $container = $configurator->createContainer();

        return $container;
    }

    private function createAndReturnTempDir() : string
    {
        $tempDir = sys_get_temp_dir() . '/nette-database-filters';
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);
        return $tempDir;
    }
}
