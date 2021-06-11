<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckRequiredClassInAnnotationRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Missing\CheckRequiredClassInAnnotationRule\Source\SomeAnnotation;

/**
 * @SomeAnnotation(checked_key=Blemc::SOME_CONSTANT)
 */
final class NonExistingClassAnnotationInConstantFetch
{
}
