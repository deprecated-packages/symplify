<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationBracesFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Annotation\NewlineInNestedAnnotationFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(DoctrineAnnotationIndentationFixer::class)
        ->call('configure', [[
            'indent_mixed_lines' => true,
        ]]);

    $services->set(DoctrineAnnotationBracesFixer::class);
    $services->set(DoctrineAnnotationSpacesFixer::class)
        ->call('configure', [[
            'after_array_assignments_equals' => true,
            'before_array_assignments_equals' => true,
        ]]);

    $services->set(DoctrineAnnotationArrayAssignmentFixer::class);
    $services->set(NewlineInNestedAnnotationFixer::class);
};
