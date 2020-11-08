<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\ContainerBuilderFactory;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SymfonyContainerBuilderFactory
{
    public function createFromConfig(SmartFileInfo $phpConfigFileInfo): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(
            $phpConfigFileInfo->getRealPathDirectory()
        ));
        $phpFileLoader->load($phpConfigFileInfo->getFilename());

        return $containerBuilder;
    }
}
