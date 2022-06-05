<?php

declare(strict_types=1);

use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\ActiveClass\ClassNameResolver;
use Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use Symplify\NeonConfigDumper\Command\DumpCommand;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\NeonConfigDumper\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Kernel']);

    // php-parser
    $services->set(ParserFactory::class);
    $services->set(Parser::class)
        ->factory([service(ParserFactory::class), 'create'])
        ->args([ParserFactory::PREFER_PHP7]);

    $services->set(Standard::class);

    $services->set(ClassNameResolver::class);
    $services->set(FullyQualifiedNameNodeDecorator::class);

    // console
    $services->set(Application::class)
        ->public()
        ->call('add', [service(DumpCommand::class)])
        ->call('setDefaultCommand', [CommandNaming::classToName(DumpCommand::class), true]);
};
