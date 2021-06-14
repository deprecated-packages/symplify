<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\Source\SomeAnnotation;

/**
 * @SomeAnnotation(checked_key=Blemc::SOME_CONSTANT)
 */
final class NonExistingClassAnnotationInConstantFetch
{
}
