<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer;
use Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Source\SomeAnnotations;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(DoctrineAnnotationNestedBracketsFixer::class)
        ->call('configure', [[
            DoctrineAnnotationNestedBracketsFixer::ANNOTATION_CLASSES => [SomeAnnotations::class],
        ]]);
};
