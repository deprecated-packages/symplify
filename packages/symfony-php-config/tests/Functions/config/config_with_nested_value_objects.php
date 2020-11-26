<?php

declare(strict_types=1);

namespace Symplify\SymfonyPhpConfig\Tests\Functions\config;

use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\Tests\Functions\Source\ServiceWithValueObject;
use Symplify\SymfonyPhpConfig\Tests\Functions\Source\WithType;
use function Symplify\SymfonyPhpConfig\inline_value_object;
use function Symplify\SymfonyPhpConfig\inline_value_objects;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $withType = new WithType(new IntegerType());

    $services->set(ServiceWithValueObject::class)
        ->call('setWithType', [inline_value_object($withType)])
        ->call('setWithTypes', [inline_value_objects([new WithType(new StringType())])]);
};
