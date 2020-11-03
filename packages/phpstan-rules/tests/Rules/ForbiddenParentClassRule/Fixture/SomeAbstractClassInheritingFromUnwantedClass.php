<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Source\UnwantedClass;

abstract class SomeAbstractClassInheritingFromUnwantedClass extends UnwantedClass
{
}
