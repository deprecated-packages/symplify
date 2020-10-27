<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\Skipper\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, []);
    $parameters->set(Option::ONLY, []);

    // @deprecated - merge with "SKIP" and remove before release
    $parameters->set(Option::EXCLUDE_PATHS, []);

    $services = $containerConfigurator->services();
    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\Skipper\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Bundle', __DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject']);

    $services->set(ClassLikeExistenceChecker::class);
};
