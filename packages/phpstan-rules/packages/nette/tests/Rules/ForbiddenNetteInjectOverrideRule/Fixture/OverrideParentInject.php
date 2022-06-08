<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source\AbstractInjectParentClass;
use Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source\AnotherType;

final class OverrideParentInject extends AbstractInjectParentClass
{
    public function __construct(AnotherType $anotherType)
    {
        $this->someParentType = $anotherType;
    }
}
