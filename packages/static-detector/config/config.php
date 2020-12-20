<?php

declare(strict_types=1);

use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PHPStanRules\Naming\NameNodeResolver\ClassLikeNameNodeResolver;
use Symplify\PHPStanRules\Naming\NameNodeResolver\IdentifierNameNodeResolver;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
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

    $services->set(ParameterProvider::class);

    $services->set(SimpleNameResolver::class);
    $services->set(ClassLikeNameNodeResolver::class);
    $services->set(IdentifierNameNodeResolver::class);
};
