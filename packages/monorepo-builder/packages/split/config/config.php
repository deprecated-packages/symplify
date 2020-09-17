<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\SmartFileSystem\FileSystemGuard;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('directories_to_repositories', []);

    $parameters->set(Option::SUBSPLIT_CACHE_DIRECTORY, '%kernel.cache_dir%/_subsplit');

    $parameters->set('env(GITHUB_TOKEN)', null);

    $parameters->set(Option::GITHUB_TOKEN, '%env(GITHUB_TOKEN)%');
    $parameters->set(Option::REPOSITORY, '%root_directory%/.git');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\MonorepoBuilder\Split\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Exception', __DIR__ . '/../src/ValueObject']);

    $services->set(FileSystemGuard::class);
};
