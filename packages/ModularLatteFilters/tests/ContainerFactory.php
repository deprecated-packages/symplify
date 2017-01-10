<?php declare(strict_types=1);

namespace Zenify\ModularLatteFilters\Tests;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\Utils\FileSystem;

final class ContainerFactory
{

    public function createWithConfig(string $config) : Container
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory($this->createAndReturnTempDir());
        $configurator->addConfig($config);
        return $configurator->createContainer();
    }


    private function createAndReturnTempDir() : string
    {
        $tempDir = sys_get_temp_dir() . '/modular-latte-filters';
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);
        return $tempDir;
    }
}
