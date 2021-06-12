<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\Source\SomeAnnotation;
use Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\Source\ExistingClass;

/**
 * @see \Nette\Utils\Strings::findPrefix()
 */
final class SkipExistingClassMethod
{
}
