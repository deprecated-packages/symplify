<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ChangelogLinker\ValueObject\Option;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\PackageBuilder\Yaml\ParametersMerger;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileSystem;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTHORS_TO_IGNORE, []);
    $parameters->set(Option::NAMES_TO_URLS, []);
    $parameters->set(Option::PACKAGE_ALIASES, []);
    $parameters->set('env(GITHUB_TOKEN)', null);
    $parameters->set(Option::GITHUB_TOKEN, '%env(GITHUB_TOKEN)%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->set(FileSystemGuard::class);

    $services->load('Symplify\\ChangelogLinker\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/HttpKernel',
            __DIR__ . '/../src/DependencyInjection/CompilerPass',
            __DIR__ . '/../src/Exception',
            __DIR__ . '/../src/ValueObject',
        ])
    ;

    $services->set(ParametersMerger::class);

    $services->set(SymfonyStyleFactory::class);

    $services->set(SymfonyStyle::class)
        ->factory([service(SymfonyStyleFactory::class), 'create']);

    $services->set(Client::class);

    $services->alias(ClientInterface::class, Client::class);

    $services->set(SmartFileSystem::class);
};
