<?php

declare(strict_types=1);

use PHP_CodeSniffer\Fixer;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Differ\UnifiedDiffer;
use PhpCsFixer\WhitespacesFixerConfig;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\SmartFileSystem\FileSystemFilter;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileSystem;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\EasyCodingStandard\\', __DIR__ . '/../src')
        ->exclude([
            // only for "bin/ecs" file, where container does not exist yet
            __DIR__ . '/../src/Bundle',
            __DIR__ . '/../src/Bootstrap',
            __DIR__ . '/../src/DependencyInjection',
            __DIR__ . '/../src/HttpKernel',
            __DIR__ . '/../src/Exception',
            __DIR__ . '/../src/ValueObject',
            // for 3rd party tests
            __DIR__ . '/../src/Testing',
        ]);

    $services->set(Terminal::class);

    $services->set(FileSystemGuard::class);
    $services->set(FinderSanitizer::class);
    $services->set(SmartFileSystem::class);
    $services->set(SmartFinder::class);
    $services->set(FileSystemFilter::class);

    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)
        ->factory([service(SymfonyStyleFactory::class), 'create']);

    $services->set(EasyCodingStandardStyle::class)
        ->factory([service(EasyCodingStandardStyleFactory::class), 'create']);

    $services->set(WhitespacesFixerConfig::class)
        ->factory([service(WhitespacesFixerConfigFactory::class), 'create']);

    // code sniffer
    $services->set(Fixer::class);

    // fixer
    $services->set(UnifiedDiffer::class);
    $services->alias(DifferInterface::class, UnifiedDiffer::class);
    $services->set(FixerFileProcessor::class);
};
