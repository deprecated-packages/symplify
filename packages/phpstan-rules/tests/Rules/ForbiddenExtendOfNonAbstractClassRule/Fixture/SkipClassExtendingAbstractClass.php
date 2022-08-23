<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenExtendOfNonAbstractClassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenExtendOfNonAbstractClassRule\Source\SimpleAbstractClass;

final class SkipClassExtendingAbstractClass extends SimpleAbstractClass
{
}
