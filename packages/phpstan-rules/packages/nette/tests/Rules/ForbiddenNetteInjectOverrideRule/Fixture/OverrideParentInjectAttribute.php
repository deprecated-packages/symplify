<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source\AbstractInjectParentClass;

final class OverrideParentInjectAttribute extends AbstractInjectParentClass
{
    public function __construct(AnotherType $anotherType)
    {
        $this->someAttributeType = $anotherType;
    }
}
