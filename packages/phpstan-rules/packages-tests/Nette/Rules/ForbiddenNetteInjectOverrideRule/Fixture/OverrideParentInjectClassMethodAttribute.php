<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Source\AbstractParentClassWithMethodInject;
use Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Source\AnotherType;

final class OverrideParentInjectClassMethodAttribute extends AbstractParentClassWithMethodInject
{
    public function __construct(AnotherType $anotherType)
    {
        $this->someType = $anotherType;
    }
}
