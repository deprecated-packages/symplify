<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Source\AbstractParentAnnotatedProperty;

final class SkipParentAnnotatedProperty extends AbstractParentAnnotatedProperty
{
    public function run($name)
    {
        $this->payload->user = $name;
    }
}
