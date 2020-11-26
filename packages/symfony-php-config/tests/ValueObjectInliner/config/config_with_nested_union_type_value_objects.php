<?php

declare(strict_types=1);

namespace Symplify\SymfonyPhpConfig\Tests\ValueObjectInliner\config;

use PHPStan\Type\NullType;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;
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

    $unionType = new UnionType([new StringType(), new NullType()]);

    $services->set(ServiceWithValueObject::class)
        ->call('setWithType', [ValueObjectInliner::inline(new WithType($unionType))]);
};
