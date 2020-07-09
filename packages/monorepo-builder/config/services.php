<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PackageBuilder\Yaml\ParametersMerger;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('Symplify\\MonorepoBuilder\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Exception/*', __DIR__ . '/../src/HttpKernel/*'])
    ;

    $services->set(EventDispatcher::class);

    $services->alias('Symfony\Component\EventDispatcher\EventDispatcherInterface', EventDispatcher::class);

    $services->set(Filesystem::class);

    $services->set(FileSystemGuard::class);

    $services->set(FinderSanitizer::class);

    $services->set(PrivatesCaller::class);

    $services->set(ParametersMerger::class);

    $services->set(SymfonyStyleFactory::class);

    $services->set(SymfonyStyle::class)
        ->factory([service(SymfonyStyleFactory::class), 'create'])
    ;
};
