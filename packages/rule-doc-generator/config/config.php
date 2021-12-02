<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Neon\NeonPrinter;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\RuleDocGenerator\Command\GenerateCommand;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\RuleDocGenerator\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);

    $services->set(Application::class)
        ->call('add', [service(GenerateCommand::class)]);

    $services->set(NeonPrinter::class);
    $services->set(ClassLikeExistenceChecker::class);
};
