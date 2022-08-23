<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenExtendOfNonAbstractClassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenExtendOfNonAbstractClassRule\Source\NonAbstractClass;

final class ClassExtendingNonAbstractClass extends NonAbstractClass
{
}
