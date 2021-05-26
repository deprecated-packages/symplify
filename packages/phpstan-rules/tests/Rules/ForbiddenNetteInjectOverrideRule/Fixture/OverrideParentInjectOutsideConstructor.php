<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source\AbstractInjectParentClass;

final class OverrideParentInjectOutsideConstructor extends AbstractInjectParentClass
{
    public function anywhere(AnotherType $anotherType)
    {
        $this->someType = $anotherType;
    }
}
