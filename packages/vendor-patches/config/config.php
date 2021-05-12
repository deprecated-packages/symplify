<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Composer\VendorDirProvider;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\SmartFileSystem\Json\JsonFileSystem;
use Symplify\VendorPatches\Console\VendorPatchesConsoleApplication;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\VendorPatches\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject']);

    $services->set(UnifiedDiffOutputBuilder::class)
        ->args([
            '$addLineNumbers' => true,
        ]);

    $services->set(Differ::class)
        ->args([
            '$outputBuilder' => service(UnifiedDiffOutputBuilder::class),
        ]);

    $services->set(VendorDirProvider::class);
    $services->set(JsonFileSystem::class);

    $services->alias(Application::class, VendorPatchesConsoleApplication::class);
    $services->set(CommandNaming::class);
};
