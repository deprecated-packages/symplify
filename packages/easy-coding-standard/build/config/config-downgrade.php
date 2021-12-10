<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\DowngradePhp72\Rector\ClassMethod\DowngradeParameterTypeWideningRector;
use Rector\DowngradePhp80\Rector\Class_\DowngradeAttributeToAnnotationRector;
use Rector\DowngradePhp80\ValueObject\DowngradeAttributeToAnnotation;
use Rector\Set\ValueObject\DowngradeLevelSetList;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\Service\Attribute\Required;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(DowngradeLevelSetList::DOWN_TO_PHP_71);

    $services = $containerConfigurator->services();

    $services->set(DowngradeParameterTypeWideningRector::class)
        ->configure([
            DowngradeParameterTypeWideningRector::UNSAFE_TYPES_TO_METHODS => [
                LoaderInterface::class => ['load'],
                Loader::class => ['import'],
            ],
        ]);

    $services->set(DowngradeAttributeToAnnotationRector::class)
        ->configure([new DowngradeAttributeToAnnotation(Required::class, 'required')]);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SKIP, [
        '*/Tests/*',
        '*/tests/*',
        __DIR__ . '/../../tests',
        # missing "optional" dependency and never used here
        '*/symfony/framework-bundle/KernelBrowser.php',
    ]);
};
