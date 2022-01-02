<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenTraitUseRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenTraitUseRule\Source\SomeSmartObjectTrait;

final class ClassWithSmartObject
{
    use SomeSmartObjectTrait;
}
