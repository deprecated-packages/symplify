<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Adapter\Nette;

use Nette\Configurator;
use Nette\DI\Container;
use Nette\Utils\FileSystem;

final class GeneralContainerFactory
{
    public function createFromConfig(string $config): Container
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory($this->createAndReturnTempDir($config));
        $configurator->addConfig($config);
        $configurator->setDebugMode(true);

        return $configurator->createContainer();
    }

    private function createAndReturnTempDir(string $config): string
    {
        $tempDir = sys_get_temp_dir() . '/' . sha1($config);
        FileSystem::delete($tempDir);
        FileSystem::createDir($tempDir);

        return $tempDir;
    }
}
