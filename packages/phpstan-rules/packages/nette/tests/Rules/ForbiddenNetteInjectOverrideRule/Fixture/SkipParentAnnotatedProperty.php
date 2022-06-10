<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source\AbstractParentAnnotatedProperty;

final class SkipParentAnnotatedProperty extends AbstractParentAnnotatedProperty
{
    public function run($name)
    {
        $this->payload->user = $name;
    }
}
