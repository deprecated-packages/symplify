<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\\EasyHydrator\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/DepencencyInjection/*',
            __DIR__ . '/../src/EasyHydratorBundle.php',
        ]);

    $services->set(FilesystemAdapter::class);

    $services->set(StringFormatConverter::class);

    $services->set(ArrayToValueObjectHydrator::class);
};
