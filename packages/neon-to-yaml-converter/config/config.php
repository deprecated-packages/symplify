<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\NeonToYamlConverter\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel/NeonToYamlKernel.php']);

    $services->set(StringFormatConverter::class);
};
