<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckRequiredClassInAnnotationRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Missing\CheckRequiredClassInAnnotationRule\Source\SomeAnnotation;
use Symplify\PHPStanRules\Tests\Rules\Missing\CheckRequiredClassInAnnotationRule\Source\ExistingClass;

/**
 * @SomeAnnotation(checked_key=ExistingClass::class)
 */
final class SkipExistingClassAnnotation
{
}
