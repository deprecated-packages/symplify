<?php

declare(strict_types=1);

namespace Symplify\SymfonyPhpConfig\Tests\Functions\config;

use PHPStan\Type\NullType;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\Tests\Functions\Source\ServiceWithValueObject;
use Symplify\SymfonyPhpConfig\Tests\Functions\Source\WithType;
use function Symplify\SymfonyPhpConfig\inline_value_object;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $unionType = new UnionType([new StringType(), new NullType()]);

    $services->set(ServiceWithValueObject::class)
        ->call('setWithType', [inline_value_object(new WithType($unionType))]);
};
