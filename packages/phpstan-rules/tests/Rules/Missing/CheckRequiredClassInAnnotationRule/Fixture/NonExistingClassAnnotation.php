<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckRequiredClassInAnnotationRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Missing\CheckRequiredClassInAnnotationRule\Source\SomeAnnotation;

/**
 * @SomeAnnotation(checked_key=Blemc::class)
 */
final class NonExistingClassAnnotation
{
}
