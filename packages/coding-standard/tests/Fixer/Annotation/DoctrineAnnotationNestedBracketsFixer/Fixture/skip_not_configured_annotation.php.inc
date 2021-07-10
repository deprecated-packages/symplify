<?php

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Source\MissingYou;
use Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Source\SomeAnnotation;

/**
 * @MissingYou(
 *     @SomeAnnotation(),
 *     @SomeAnnotation()
 * )
 */
final class SkipNotConfiguredAnnotation
{
}
