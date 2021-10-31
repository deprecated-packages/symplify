<?php

declare(strict_types=1);

use PhpParser\BuilderFactory;
use PhpParser\NodeFinder;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Yaml\Parser;
use Symplify\ConfigTransformer\Console\ConfigTransfomerConsoleApplication;
use Symplify\ConfigTransformer\Provider\YamlContentProvider;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\PackageBuilder\Yaml\ParametersMerger;
use Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;
use Symplify\SmartFileSystem\FileSystemFilter;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\ConfigTransformer\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/HttpKernel',
            __DIR__ . '/../src/DependencyInjection/Loader',
            __DIR__ . '/../src/Enum',
            __DIR__ . '/../src/ValueObject',
        ]);

    // console
    $services->set(ConfigTransfomerConsoleApplication::class);
    $services->alias(Application::class, ConfigTransfomerConsoleApplication::class);

    $services->set(BuilderFactory::class);
    $services->set(NodeFinder::class);
    $services->set(Parser::class);
    $services->set(FileSystemFilter::class);

    $services->alias(YamlFileContentProviderInterface::class, YamlContentProvider::class);

    $services->set(ClassLikeExistenceChecker::class);
    $services->set(ParametersMerger::class);
};
