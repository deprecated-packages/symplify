<?php declare(strict_types=1);

namespace Symplify\DoctrineExtensionsTree\Tests;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\Utils\FileSystem;

final class ContainerFactory
{
    public function create(): Container
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory($this->createAndReturnTempDir());
        $configurator->addConfig(__DIR__ . '/config/default.neon');
        return $configurator->createContainer();
    }

    private function createAndReturnTempDir(): string
    {
        $tempDir = sys_get_temp_dir() . '/dictrine-extensions-tree';
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);
        return $tempDir;
    }
}
