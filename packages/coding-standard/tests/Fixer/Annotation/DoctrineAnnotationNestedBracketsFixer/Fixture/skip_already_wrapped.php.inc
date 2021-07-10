<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Source\SomeAnnotation;
use Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Source\SomeAnnotations;

/**
 * @SomeAnnotations({
 *     @SomeAnnotation(),
 *     @SomeAnnotation()
 * })
 */
final class SkipAlreadyWrapped
{
}
