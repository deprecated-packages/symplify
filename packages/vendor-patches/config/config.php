<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Composer\VendorDirProvider;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

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
            '$outputBuilder' => ref(UnifiedDiffOutputBuilder::class),
        ]);

    $services->set(VendorDirProvider::class);
};
