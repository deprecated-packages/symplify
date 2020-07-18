<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('is_stage_required', false);

    $parameters->set('stages_to_allow_existing_tag', []);

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->public();

    $services->load('Symplify\MonorepoBuilder\Release\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/Exception/*',
            __DIR__ . '/../src/ReleaseWorker/*',
        ]);
};
