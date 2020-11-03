<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParentClassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenParentClassRule\Source\UnwantedClass;

abstract class SomeAbstractClassInheritingFromUnwantedClass extends UnwantedClass
{
}
