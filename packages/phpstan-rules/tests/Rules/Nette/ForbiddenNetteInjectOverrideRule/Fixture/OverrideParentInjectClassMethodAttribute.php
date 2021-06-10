<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Nette\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Nette\ForbiddenNetteInjectOverrideRule\Source\AbstractParentClassWithMethodInject;

final class OverrideParentInjectClassMethodAttribute extends AbstractParentClassWithMethodInject
{
    public function __construct(AnotherType $anotherType)
    {
        $this->someType = $anotherType;
    }
}
