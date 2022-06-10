<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Source\AbstractInjectParentClass;
use Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Source\AnotherType;

final class OverrideParentInject extends AbstractInjectParentClass
{
    public function __construct(AnotherType $anotherType)
    {
        $this->someParentType = $anotherType;
    }
}
