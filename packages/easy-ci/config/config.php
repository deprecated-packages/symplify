<?php

declare(strict_types=1);

use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use Nette\Neon\Decoder;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\Console\EasyCIApplication;
use Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser;
use Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverserFactory;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\EasyCI\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/Kernel',
            __DIR__ . '/../src/ValueObject',
            __DIR__ . '/../src/Config/EasyCIConfig.php',
        ]);

    $services->load('Symplify\EasyCI\\', __DIR__ . '/../packages')
        ->exclude([
            __DIR__ . '/../packages/StaticDetector/ValueObject',
            __DIR__ . '/../packages/ActiveClass/ValueObject',
            __DIR__ . '/../packages/Psr4/ValueObject',
        ]);

    // for autowired commands
    $services->alias(Application::class, EasyCIApplication::class);

    $services->set(VersionParser::class);
    $services->set(Semver::class);

    // neon
    $services->set(Decoder::class);

    // php-parser
    $services->set(ParserFactory::class);
    $services->set(Parser::class)
        ->factory([service(ParserFactory::class), 'create'])
        ->args([ParserFactory::PREFER_PHP7]);

    $services->set(Standard::class);
    $services->set(NodeFinder::class);
    $services->set(ClassLikeExistenceChecker::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::TYPES_TO_SKIP, []);
    $parameters->set(Option::EXCLUDED_CHECK_PATHS, []);

    $services->set(StaticCollectNodeTraverser::class)
        ->factory([service(StaticCollectNodeTraverserFactory::class), 'create']);

    $services->set(ParserFactory::class);
    $services->set(Parser::class)
        ->factory([service(ParserFactory::class), 'create'])
        ->arg('$kind', ParserFactory::PREFER_PHP7);

    $services->set(ParameterProvider::class)
        ->args([service('service_container')]);
};
