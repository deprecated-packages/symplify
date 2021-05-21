<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectPropertyAssignRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectPropertyAssignRule\Source\AbstractInjectParentClass;

final class OverrideParentInject extends AbstractInjectParentClass
{
    public function __construct(AnotherType $anotherType)
    {
        $this->someType = $anotherType;
    }
}
