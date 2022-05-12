<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DowngradePhp72\Rector\ClassMethod\DowngradeParameterTypeWideningRector;
use Rector\DowngradePhp80\Rector\Class_\DowngradeAttributeToAnnotationRector;
use Rector\DowngradePhp80\ValueObject\DowngradeAttributeToAnnotation;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Contracts\Service\Attribute\Required;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([DowngradeLevelSetList::DOWN_TO_PHP_71]);

    $rectorConfig->ruleWithConfiguration(DowngradeParameterTypeWideningRector::class, [
        LoaderInterface::class => ['load'],
        Loader::class => ['import'],
    ]);

    $rectorConfig->ruleWithConfiguration(DowngradeAttributeToAnnotationRector::class, [
        new DowngradeAttributeToAnnotation(Required::class, 'required'),
    ]);

    $rectorConfig->skip([
        '*/Tests/*',
        '*/tests/*',
        __DIR__ . '/../../tests',
        # missing "optional" dependency and never used here
        '*/symfony/framework-bundle/KernelBrowser.php',
    ]);
};
