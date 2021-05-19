<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Source\SomeRectorInterface;

final class RemoveParentType implements SomeRectorInterface
{
    public function refactor($node)
    {
    }
}
