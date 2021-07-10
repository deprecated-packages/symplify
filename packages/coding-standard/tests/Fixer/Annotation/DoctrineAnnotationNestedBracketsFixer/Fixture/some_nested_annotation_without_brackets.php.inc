<?php

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Source\SomeAnnotations;
use Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Source\SomeAnnotation;

/**
 * @SomeAnnotations(
 *     @SomeAnnotation(),
 *     @SomeAnnotation()
 * )
 */
class SomeNestedAnnotationWithoutBrackets
{
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Fixture;

use Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Source\SomeAnnotations;
use Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNestedBracketsFixer\Source\SomeAnnotation;

/**
 * @SomeAnnotations({
 *     @SomeAnnotation(),
 *     @SomeAnnotation()
 * })
 */
class SomeNestedAnnotationWithoutBrackets
{
}

?>
