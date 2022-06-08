<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source\AbstractParentClassWithMethodInject;
use Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source\AnotherType;

final class OverrideParentInjectClassMethodAttribute extends AbstractParentClassWithMethodInject
{
    public function __construct(AnotherType $anotherType)
    {
        $this->someType = $anotherType;
    }
}
