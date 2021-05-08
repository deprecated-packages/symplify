<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\DowngradePhp70\Rector\FunctionLike\DowngradeTypeDeclarationRector;
use Rector\DowngradePhp72\Rector\Class_\DowngradeParameterTypeWideningRector;
use Rector\DowngradePhp74\Rector\ClassMethod\DowngradeContravariantArgumentTypeRector;
use Rector\Set\ValueObject\DowngradeSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(DowngradeSetList::PHP_80);
    $containerConfigurator->import(DowngradeSetList::PHP_74);
    $containerConfigurator->import(DowngradeSetList::PHP_73);
    $containerConfigurator->import(DowngradeSetList::PHP_72);
    $containerConfigurator->import(DowngradeSetList::PHP_71);
    $containerConfigurator->import(DowngradeSetList::PHP_70);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, [
        '*/Tests/*', '*/tests/*', __DIR__ . '/../../tests',
        DowngradeParameterTypeWideningRector::class,
        //        DowngradeTypeDeclarationRector::class,
        DowngradeContravariantArgumentTypeRector::class,
    ]);
};
