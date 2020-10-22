<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Split\ValueObject\ConvertFormat;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Strings\StringFormatConverter;
use Symplify\SmartFileSystem\FileSystemGuard;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES, []);
    $parameters->set(Option::DIRECTORIES_TO_REPOSITORIES_CONVERT_FORMAT, ConvertFormat::EQUAL);

    $parameters->set(Option::SUBSPLIT_CACHE_DIRECTORY, '%kernel.cache_dir%/_subsplit');

    $parameters->set('env(GITHUB_TOKEN)', null);

    $parameters->set(Option::GITHUB_TOKEN, '%env(GITHUB_TOKEN)%');
    $parameters->set(Option::REPOSITORY, '%root_directory%/.git');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->load('Symplify\MonorepoBuilder\Split\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Exception', __DIR__ . '/../src/ValueObject']);

    $services->set(FileSystemGuard::class);
    $services->set(StringFormatConverter::class);
};
