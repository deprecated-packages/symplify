<?php

declare(strict_types=1);

namespace Symplify\SymfonySecurity\Tests\Adapter\Nette;

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
        $configurator->setTempDirectory($this->createAndReturnTempDirectory());
        $configurator->addConfig($config);

        return $configurator->createContainer();
    }

    private function createAndReturnTempDirectory() : string
    {
        $tempDir = sys_get_temp_dir() . '/symplify_symfony_security';
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);

        return $tempDir;
    }
}
