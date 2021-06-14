<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\Source\SomeAnnotation;
use Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\Source\ExistingClass;

/**
 * @SomeAnnotation(checked_key=ExistingClass::class)
 */
final class SkipExistingClassAnnotation
{
}
