<?php

declare(strict_types=1);

use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\StaticDetector\Console\StaticDetectorConsoleApplication;
use Symplify\StaticDetector\NodeTraverser\StaticCollectNodeTraverser;
use Symplify\StaticDetector\NodeTraverser\StaticCollectNodeTraverserFactory;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\StaticDetector\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/ValueObject', __DIR__ . '/../src/HttpKernel/StaticDetectorKernel.php']);

    $services->set(StaticCollectNodeTraverser::class)
        ->factory([service(StaticCollectNodeTraverserFactory::class), 'create']);

    $services->set(ParserFactory::class);
    $services->set(Parser::class)
        ->factory([service(ParserFactory::class), 'create'])
        ->arg('$kind', ParserFactory::PREFER_PHP7);

    $services->set(ParameterProvider::class)
        ->args([service(ContainerInterface::class)]);

    $services->alias(Application::class, StaticDetectorConsoleApplication::class);
    $services->set(CommandNaming::class);
};
