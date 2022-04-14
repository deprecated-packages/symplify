<?php

declare(strict_types=1);

use Symplify\CodingStandard\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer;
use Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Source\SomeAnnotations;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(DoctrineAnnotationNestedBracketsFixer::class, [
        DoctrineAnnotationNestedBracketsFixer::ANNOTATION_CLASSES => [SomeAnnotations::class],
    ]);
};
