<?php

declare(strict_types=1);

use PhpParser\Parser;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser;
use Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverserFactory;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::TYPES_TO_SKIP, []);
    $parameters->set(Option::EXCLUDED_CHECK_PATHS, []);

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\EasyCI\\', __DIR__ . '/../packages')
        ->exclude([
            __DIR__ . '/../packages/StaticDetector/ValueObject',
            __DIR__ . '/../packages/ActiveClass/ValueObject',
        ]);

    $services->set(StaticCollectNodeTraverser::class)
        ->factory([service(StaticCollectNodeTraverserFactory::class), 'create']);

    $services->set(ParserFactory::class);
    $services->set(Parser::class)
        ->factory([service(ParserFactory::class), 'create'])
        ->arg('$kind', ParserFactory::PREFER_PHP7);

    $services->set(ParameterProvider::class)
        ->args([service(ContainerInterface::class)]);
};
