<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(DoctrineAnnotationNewlineInNestedAnnotationFixer::class);
};
