<?php

declare(strict_types=1);

namespace Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner\config;

use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner\Source\ServiceWithValueObject;
use Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner\Source\WithType;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $withType = new WithType(new IntegerType());

    $services->set(ServiceWithValueObject::class)
        ->call('setWithType', [ValueObjectInliner::inline($withType)])
        ->call('setWithTypes', [ValueObjectInliner::inline([new WithType(new StringType())])]);
};
